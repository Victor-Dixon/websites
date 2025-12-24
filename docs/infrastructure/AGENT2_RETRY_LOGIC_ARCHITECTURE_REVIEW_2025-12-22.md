# Retry Logic Architecture Review & Design

**Author:** Agent-2 (Architecture & Design Specialist)  
**Date:** 2025-12-22  
**Status:** Architecture Review Complete - Ready for Implementation  
**Task:** Add retry logic to WordPress deployment tools with exponential backoff

<!-- SSOT Domain: infrastructure -->

## Executive Summary

This document provides architectural guidance for implementing retry logic with exponential backoff in WordPress deployment tools, specifically `SimpleWordPressDeployer`. The review analyzes current error handling, identifies retry opportunities, and provides a standardized approach for resilient deployment operations.

**Current Status:**
- ✅ Error handling exists (AuthenticationException, SSHException, generic Exception)
- ✅ Detailed error messages implemented
- ❌ No retry logic for transient failures
- ❌ No exponential backoff strategy
- ❌ No connection retry mechanism

**Recommendation:** Implement configurable retry decorator with exponential backoff, retryable exception classification, and operation-specific retry strategies.

---

## 1. Current State Analysis

### 1.1 Error Handling in SimpleWordPressDeployer

**Current Implementation:**
```python
# Connection errors (lines 214-234)
except paramiko.AuthenticationException as e:
    # Fails immediately, no retry
except paramiko.SSHException as e:
    # Fails immediately, no retry
except Exception as e:
    # Fails immediately, no retry

# SFTP upload errors (lines 294-299)
except paramiko.SSHException as e:
    # Fails immediately, no retry
```

**Analysis:**
- ✅ Good error classification (AuthenticationException, SSHException)
- ✅ Detailed error messages with diagnostics
- ❌ No distinction between transient and permanent failures
- ❌ No retry mechanism for transient failures
- ❌ No backoff strategy

### 1.2 Retry Opportunities

**Transient Failures (Should Retry):**
1. **Network timeouts** - Temporary network issues
2. **SSH connection failures** - Server temporarily unavailable
3. **SFTP upload failures** - Transient connection drops
4. **Rate limiting** - Temporary server-side throttling
5. **Temporary authentication issues** - Token expiration, refresh needed

**Permanent Failures (Should Not Retry):**
1. **Invalid credentials** - AuthenticationException (wrong username/password)
2. **Invalid file paths** - FileNotFoundError
3. **Permission errors** - Insufficient file permissions
4. **Syntax errors** - PHP syntax errors in files
5. **Invalid configuration** - Missing required config values

---

## 2. Architecture Design

### 2.1 Design Principles

1. **Separation of Concerns:** Retry logic separate from business logic
2. **Configurability:** Retry parameters configurable per operation
3. **Exception Classification:** Distinguish retryable vs non-retryable exceptions
4. **Exponential Backoff:** Progressive delay between retries
5. **Operation-Specific:** Different retry strategies for connection vs upload
6. **Observability:** Log retry attempts and outcomes

### 2.2 Recommended Architecture

#### Option A: Decorator Pattern (Recommended)
**Structure:**
```python
# Retry decorator module
retry_decorator.py
  - retry_with_backoff() decorator
  - RetryableException base class
  - RetryConfig dataclass
  - Exponential backoff calculator

# Integration
simple_wordpress_deployer.py
  - Uses @retry_with_backoff decorator
  - Classifies exceptions as retryable/non-retryable
  - Configures retry parameters per operation
```

**Advantages:**
- ✅ Clean separation of concerns
- ✅ Reusable across deployment tools
- ✅ Easy to test independently
- ✅ Non-invasive to existing code

#### Option B: Class-Based Retry Manager (Alternative)
**Structure:**
```python
# Retry manager class
class RetryManager:
    def execute_with_retry(self, operation, config)
    def is_retryable(self, exception)
    def calculate_backoff(self, attempt)
```

**Advantages:**
- ✅ More control over retry logic
- ✅ Can maintain state across retries
- ⚠️ More invasive to existing code
- ⚠️ Harder to reuse

### 2.3 Retry Configuration

**Default Retry Parameters:**
```python
@dataclass
class RetryConfig:
    max_attempts: int = 3
    initial_delay: float = 1.0  # seconds
    max_delay: float = 60.0  # seconds
    exponential_base: float = 2.0
    jitter: bool = True  # Add random jitter to prevent thundering herd
    retryable_exceptions: tuple = (
        paramiko.SSHException,
        paramiko.socket.timeout,
        ConnectionError,
        TimeoutError,
    )
    non_retryable_exceptions: tuple = (
        paramiko.AuthenticationException,
        FileNotFoundError,
        PermissionError,
        ValueError,
    )
```

**Operation-Specific Configurations:**
```python
# Connection retries (more aggressive)
CONNECTION_RETRY_CONFIG = RetryConfig(
    max_attempts=5,
    initial_delay=2.0,
    max_delay=30.0,
)

# Upload retries (less aggressive)
UPLOAD_RETRY_CONFIG = RetryConfig(
    max_attempts=3,
    initial_delay=1.0,
    max_delay=10.0,
)
```

---

## 3. Implementation Specifications

### 3.1 Retry Decorator Implementation

**File:** `ops/deployment/retry_decorator.py`

**Core Decorator:**
```python
from functools import wraps
import time
import random
from typing import Callable, Type, Tuple
from dataclasses import dataclass

@dataclass
class RetryConfig:
    """Configuration for retry behavior."""
    max_attempts: int = 3
    initial_delay: float = 1.0
    max_delay: float = 60.0
    exponential_base: float = 2.0
    jitter: bool = True
    retryable_exceptions: Tuple[Type[Exception], ...] = (
        paramiko.SSHException,
        ConnectionError,
        TimeoutError,
    )
    non_retryable_exceptions: Tuple[Type[Exception], ...] = (
        paramiko.AuthenticationException,
        FileNotFoundError,
        PermissionError,
        ValueError,
    )

def retry_with_backoff(config: RetryConfig = None):
    """
    Decorator that retries a function with exponential backoff.
    
    Args:
        config: RetryConfig instance (uses defaults if None)
    
    Returns:
        Decorated function with retry logic
    """
    if config is None:
        config = RetryConfig()
    
    def decorator(func: Callable):
        @wraps(func)
        def wrapper(*args, **kwargs):
            last_exception = None
            
            for attempt in range(1, config.max_attempts + 1):
                try:
                    return func(*args, **kwargs)
                except config.non_retryable_exceptions as e:
                    # Don't retry permanent failures
                    raise
                except config.retryable_exceptions as e:
                    last_exception = e
                    if attempt < config.max_attempts:
                        delay = calculate_backoff(
                            attempt,
                            config.initial_delay,
                            config.max_delay,
                            config.exponential_base,
                            config.jitter
                        )
                        print(f"⚠️  Retry attempt {attempt}/{config.max_attempts} after {delay:.2f}s...")
                        time.sleep(delay)
                    else:
                        # Final attempt failed
                        raise
                except Exception as e:
                    # Unknown exception - check if it's a subclass of retryable
                    if any(isinstance(e, exc_type) for exc_type in config.retryable_exceptions):
                        last_exception = e
                        if attempt < config.max_attempts:
                            delay = calculate_backoff(
                                attempt,
                                config.initial_delay,
                                config.max_delay,
                                config.exponential_base,
                                config.jitter
                            )
                            print(f"⚠️  Retry attempt {attempt}/{config.max_attempts} after {delay:.2f}s...")
                            time.sleep(delay)
                        else:
                            raise
                    else:
                        # Unknown non-retryable exception
                        raise
            
            # Should never reach here, but just in case
            if last_exception:
                raise last_exception
        
        return wrapper
    return decorator

def calculate_backoff(
    attempt: int,
    initial_delay: float,
    max_delay: float,
    exponential_base: float,
    jitter: bool = True
) -> float:
    """
    Calculate exponential backoff delay.
    
    Formula: delay = min(initial_delay * (base ^ (attempt - 1)), max_delay)
    With jitter: delay = delay * (0.5 + random.random() * 0.5)
    
    Args:
        attempt: Current attempt number (1-indexed)
        initial_delay: Initial delay in seconds
        max_delay: Maximum delay in seconds
        exponential_base: Base for exponential calculation
        jitter: Whether to add random jitter
    
    Returns:
        Delay in seconds
    """
    delay = min(
        initial_delay * (exponential_base ** (attempt - 1)),
        max_delay
    )
    
    if jitter:
        # Add 0-50% random jitter
        delay = delay * (0.5 + random.random() * 0.5)
    
    return delay
```

### 3.2 Integration with SimpleWordPressDeployer

**Modified Connection Method:**
```python
from ops.deployment.retry_decorator import retry_with_backoff, RetryConfig

# Connection-specific retry config
CONNECTION_RETRY_CONFIG = RetryConfig(
    max_attempts=5,
    initial_delay=2.0,
    max_delay=30.0,
    retryable_exceptions=(
        paramiko.SSHException,
        paramiko.socket.timeout,
        ConnectionError,
        TimeoutError,
    ),
    non_retryable_exceptions=(
        paramiko.AuthenticationException,
        ValueError,
    ),
)

@retry_with_backoff(CONNECTION_RETRY_CONFIG)
def connect(self, remote_path: str = None) -> bool:
    """Connect to SFTP server with retry logic."""
    # Existing connection logic
    # Retry decorator handles transient failures automatically
    ...
```

**Modified Upload Method:**
```python
# Upload-specific retry config
UPLOAD_RETRY_CONFIG = RetryConfig(
    max_attempts=3,
    initial_delay=1.0,
    max_delay=10.0,
    retryable_exceptions=(
        paramiko.SSHException,
        paramiko.socket.timeout,
        ConnectionError,
        TimeoutError,
    ),
    non_retryable_exceptions=(
        paramiko.AuthenticationException,
        FileNotFoundError,
        PermissionError,
    ),
)

@retry_with_backoff(UPLOAD_RETRY_CONFIG)
def deploy_file(self, local_path: Path, remote_path: str = None) -> bool:
    """Deploy a file with retry logic."""
    # Existing upload logic
    # Retry decorator handles transient failures automatically
    ...
```

### 3.3 Exception Classification

**Retryable Exceptions:**
- `paramiko.SSHException` - Transient SSH connection issues
- `paramiko.socket.timeout` - Network timeouts
- `ConnectionError` - General connection failures
- `TimeoutError` - Operation timeouts
- `OSError` (network-related) - Transient OS-level network errors

**Non-Retryable Exceptions:**
- `paramiko.AuthenticationException` - Invalid credentials (permanent)
- `FileNotFoundError` - File doesn't exist (permanent)
- `PermissionError` - Insufficient permissions (permanent)
- `ValueError` - Invalid configuration (permanent)
- `SyntaxError` - PHP syntax errors (permanent)

---

## 4. Implementation Roadmap

### Phase 1: Core Retry Decorator (Week 1)
**Goal:** Create reusable retry decorator module

**Tasks:**
1. Create `ops/deployment/retry_decorator.py`
2. Implement `RetryConfig` dataclass
3. Implement `retry_with_backoff` decorator
4. Implement `calculate_backoff` function
5. Add unit tests for retry logic

**Deliverables:**
- Retry decorator module
- Unit tests
- Documentation

### Phase 2: Integration (Week 1)
**Goal:** Integrate retry logic into SimpleWordPressDeployer

**Tasks:**
1. Add retry decorator to `connect()` method
2. Add retry decorator to `deploy_file()` method
3. Configure operation-specific retry parameters
4. Test retry behavior with simulated failures
5. Update error messages to indicate retry attempts

**Deliverables:**
- Updated SimpleWordPressDeployer
- Integration tests
- Updated documentation

### Phase 3: Testing & Validation (Week 1)
**Goal:** Validate retry logic in real-world scenarios

**Tasks:**
1. Test with network interruptions
2. Test with server timeouts
3. Test with authentication failures (should not retry)
4. Test with invalid file paths (should not retry)
5. Performance testing (ensure retries don't slow down successful operations)

**Deliverables:**
- Test results
- Performance metrics
- Validation report

---

## 5. Testing Strategy

### 5.1 Unit Tests

**Test Cases:**
1. **Successful operation** - No retries needed
2. **Transient failure then success** - Retry succeeds
3. **All retries fail** - Final exception raised
4. **Non-retryable exception** - Immediate failure, no retries
5. **Backoff calculation** - Verify exponential backoff formula
6. **Jitter** - Verify random jitter added
7. **Max delay** - Verify delay capped at max_delay

### 5.2 Integration Tests

**Test Scenarios:**
1. **Network timeout simulation** - Simulate network failures
2. **SSH connection retry** - Test connection retry logic
3. **SFTP upload retry** - Test upload retry logic
4. **Mixed failures** - Test combination of retryable/non-retryable

### 5.3 Real-World Testing

**Test Cases:**
1. Deploy with unstable network connection
2. Deploy during server maintenance window
3. Deploy with rate-limited server
4. Deploy with invalid credentials (should fail immediately)

---

## 6. Risk Assessment

### 6.1 Implementation Risks

**Risk 1: Retrying Non-Retryable Failures**
- **Probability:** Low
- **Impact:** High (wasted time, confusing errors)
- **Mitigation:** Clear exception classification, comprehensive testing

**Risk 2: Too Many Retries**
- **Probability:** Medium
- **Impact:** Medium (slow deployments, resource waste)
- **Mitigation:** Configurable max_attempts, reasonable defaults

**Risk 3: Retry Logic Breaking Existing Code**
- **Probability:** Low
- **Impact:** High (deployment failures)
- **Mitigation:** Comprehensive testing, backward compatibility

### 6.2 Benefits

**Expected Improvements:**
- ✅ Resilience to transient network failures
- ✅ Automatic recovery from temporary server issues
- ✅ Reduced manual intervention for deployment failures
- ✅ Better user experience (automatic retries vs manual retries)
- ✅ Improved deployment success rate

---

## 7. Configuration Options

### 7.1 Environment Variables

**Optional Configuration:**
```python
# .env file
DEPLOYMENT_MAX_RETRIES=3
DEPLOYMENT_INITIAL_DELAY=1.0
DEPLOYMENT_MAX_DELAY=60.0
DEPLOYMENT_RETRY_JITTER=true
```

### 7.2 Site-Specific Configuration

**Per-Site Retry Settings:**
```json
{
  "site_key": {
    "retry_config": {
      "max_attempts": 5,
      "initial_delay": 2.0,
      "max_delay": 30.0
    }
  }
}
```

---

## 8. Observability & Logging

### 8.1 Retry Logging

**Log Messages:**
```python
# Retry attempt
"⚠️  Retry attempt 1/3 after 1.23s... (SSHException: Connection timeout)"

# Final failure
"❌ All retry attempts failed. Last error: SSHException: Connection timeout"

# Success after retry
"✅ Operation succeeded after 2 retry attempts"
```

### 8.2 Metrics

**Track:**
- Retry attempt counts
- Retry success rate
- Average retry delay
- Most common retryable exceptions

---

## 9. Coordination Requirements

### 9.1 Agent Responsibilities

**Agent-2 (Architecture):**
- ✅ Architecture review complete
- ✅ Design specifications provided
- ✅ Implementation roadmap defined

**Agent-1 (Implementation):**
- Implement retry decorator module
- Integrate retry logic into SimpleWordPressDeployer
- Add unit and integration tests
- Update documentation

### 9.2 Handoff Points

1. **Architecture Review → Implementation:** Agent-2 provides design, Agent-1 implements
2. **Implementation → Testing:** Agent-1 implements, Agent-1 tests
3. **Testing → Production:** Agent-1 validates, Agent-3 monitors production

---

## 10. Approval & Next Steps

### 10.1 Architecture Approval

**Status:** ✅ **APPROVED FOR IMPLEMENTATION**

**Approval Criteria Met:**
- ✅ Design follows Python best practices (decorator pattern)
- ✅ Configurable and reusable approach
- ✅ Clear exception classification
- ✅ Risk mitigation strategies defined
- ✅ Implementation roadmap clear
- ✅ Coordination responsibilities defined

### 10.2 Next Steps

1. **Agent-1:** Create `ops/deployment/retry_decorator.py` module
2. **Agent-1:** Implement `RetryConfig` dataclass and `retry_with_backoff` decorator
3. **Agent-1:** Add retry logic to `SimpleWordPressDeployer.connect()` method
4. **Agent-1:** Add retry logic to `SimpleWordPressDeployer.deploy_file()` method
5. **Agent-1:** Add unit tests for retry decorator
6. **Agent-1:** Add integration tests for deployment retry scenarios
7. **Agent-1:** Update documentation with retry behavior
8. **Agent-3:** Validate retry logic in production scenarios

---

## 11. References

- **Exponential Backoff:** https://en.wikipedia.org/wiki/Exponential_backoff
- **Paramiko Documentation:** http://www.paramiko.org/
- **Python Retry Libraries:** `tenacity`, `backoff`, `retrying`
- **Best Practices:** https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/

---

**Document Status:** Architecture Review Complete  
**Next Action:** Agent-1 implementation  
**ETA:** Phase 1-3 (Complete Implementation) - 2025-12-25

