# Preserve repair evidence and inspect deployers

Generated: 2026-06-05T01:12:12-05:00

## Action

Committed git-history repair evidence separately from deployment scripts.

## Deployer secret scan

```text
## ops/deployment/simple_wordpress_deployer.py
7:for SFTP credentials. Works without WordPressManager dependency.
25:def load_hostinger_env_credentials():
26:    """Load Hostinger credentials from environment variables or .env file."""
34:        Path.home() / ".hostinger_env",  # Home directory
42:    host = os.getenv("HOSTINGER_HOST")
43:    username = os.getenv("HOSTINGER_USER")
44:    password = os.getenv("HOSTINGER_PASS")
45:    port = int(os.getenv("HOSTINGER_PORT", "65002"))
47:    if all([host, username, password]):
50:            "username": username,
51:            "password": password,
59:    # Priority 1: Hostinger environment variables (.env)
60:    hostinger_creds = load_hostinger_env_credentials()
61:    if hostinger_creds:
62:        # Create a default config structure with Hostinger credentials
66:                "host": hostinger_creds["host"],
67:                "username": hostinger_creds["username"],
68:                "password": hostinger_creds["password"],
69:                "port": hostinger_creds["port"]
79:                # Merge with Hostinger defaults if available
80:                if hostinger_creds:
83:                            site_config['host'] = hostinger_creds['host']
84:                            site_config['username'] = hostinger_creds['username']
85:                            site_config['password'] = hostinger_creds['password']
86:                            site_config['port'] = hostinger_creds['port']
100:                # Merge with Hostinger defaults if available
101:                if hostinger_creds:
103:                        sftp_config = site_config.get('sftp', {})
104:                        if not sftp_config.get('host'):
105:                            sftp_config['host'] = hostinger_creds['host']
106:                            sftp_config['username'] = hostinger_creds['username']
107:                            sftp_config['password'] = hostinger_creds['password']
108:                            sftp_config['port'] = hostinger_creds['port']
113:    # Priority 4: Return Hostinger defaults if available
114:    if hostinger_creds:
121:    """Simple WordPress deployer using SFTP from site_configs.json"""
127:        self.sftp = None
144:        """Connect to server via SFTP."""
149:        # Try to get credentials from Hostinger environment variables first
157:            Path.home() / ".hostinger_env",  # Home directory
165:        # Check environment variables first (Hostinger tool credentials)
166:        host = os.getenv("HOSTINGER_HOST")
167:        username = os.getenv("HOSTINGER_USER")
168:        password = os.getenv("HOSTINGER_PASS")
169:        port = int(os.getenv("HOSTINGER_PORT", "65002"))
172:        if not all([host, username, password]):
174:            # sites.json format: direct keys like "host", "username", "password"
175:            # site_configs.json format: nested under "sftp" key
176:            if 'sftp' in self.site_config:
177:                sftp_config = self.site_config.get('sftp', {})
178:                host = sftp_config.get('host') or host
179:                username = sftp_config.get('username') or username
180:                password = sftp_config.get('password') or password
181:                port = sftp_config.get('port', port or 22)
182:                remote_path = sftp_config.get('remote_path', '')
186:                username = self.site_config.get('username') or username
187:                password = self.site_config.get('password') or password
192:            if 'sftp' in self.site_config:
193:                remote_path = self.site_config.get('sftp', {}).get('remote_path', '')
197:        if not all([host, username, password]):
198:            print(f"❌ Incomplete SFTP credentials for {self.site_key}")
200:            print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
201:            print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
202:            print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
203:            print(f"      - HOSTINGER_PORT: {port if port else '❌ Missing (default: 65002)'}")
206:            if 'sftp' in self.site_config:
207:                sftp_config = self.site_config.get('sftp', {})
208:                print(f"      2. site_configs.json['{self.site_key}']['sftp']")
209:                print(f"         - host: {'✅ Set' if sftp_config.get('host') else '❌ Missing'}")
210:                print(f"         - username: {'✅ Set' if sftp_config.get('username') else '❌ Missing'}")
211:                print(f"         - password: {'✅ Set' if sftp_config.get('password') else '❌ Missing'}")
215:                print(f"         - username: {'✅ Set' if self.site_config.get('username') else '❌ Missing'}")
216:                print(f"         - password: {'✅ Set' if self.site_config.get('password') else '❌ Missing'}")
217:            print("   💡 Solution: Set HOSTINGER_* environment variables in .env file or add credentials to site config")
223:            print(f"🔌 Connecting to {host}:{port} as {username}...")
225:            self.transport.connect(username=username, password=password)
226:            self.sftp = paramiko.SFTPClient.from_transport(self.transport)
232:            print(f"   Details: Invalid username or password for {username}@{host}:{port}")
235:        except paramiko.SSHException as e:
236:            print(f"❌ SSH connection error for {self.site_key}")
238:            print(f"   Details: Failed to establish SSH connection to {host}:{port}")
245:            print(f"   Connection Details: {username}@{host}:{port}")
257:            username = self.site_config.get('username') or self.site_config.get('sftp', {}).get('username', '')
258:            if username and remote_dir.startswith('domains/'):
259:                remote_dir = f"/home/{username}/{remote_dir}"
265:                self.sftp.stat(current)
267:                self.sftp.mkdir(current)
271:        if not self.sftp:
292:            # Hostinger structure: /home/username/domains/domain.com/public_html/...
295:                username = self.site_config.get('username') if 'username' in self.site_config else self.site_config.get('sftp', {}).get('username', '')
296:                if username and not full_remote_path.startswith(f'/home/{username}'):
299:                        full_remote_path = f"/home/{username}/{full_remote_path}"
304:                            full_remote_path = f"/home/{username}/{base_path}/{full_remote_path}"
311:            self.sftp.put(str(local_path), full_remote_path)
313:        except paramiko.SSHException as e:
314:            print(f"❌ SFTP upload error for {self.site_key}")
315:            print(f"   Error Type: SSHException")
322:            print(f"❌ SFTP file I/O error for {self.site_key}")
330:            print(f"❌ SFTP upload error for {self.site_key}")
341:        """Execute a command via SSH using the same credential loading as connect()."""
354:            # Check environment variables first (Hostinger tool credentials)
355:            host = os.getenv("HOSTINGER_HOST")
356:            username = os.getenv("HOSTINGER_USER")
357:            password = os.getenv("HOSTINGER_PASS")
358:            port = int(os.getenv("HOSTINGER_PORT", "65002"))  # Hostinger uses 65002
361:            if not all([host, username, password]):
362:                if 'sftp' in self.site_config:
363:                    sftp_config = self.site_config.get('sftp', {})
364:                    host = sftp_config.get('host') or host
365:                    username = sftp_config.get('username') or username
366:                    password = sftp_config.get('password') or password
367:                    port = sftp_config.get('port', port or 65002)  # Default to Hostinger port
370:                    username = self.site_config.get('username') or username
371:                    password = self.site_config.get('password') or password
372:                    port = self.site_config.get('port', port or 65002)  # Default to Hostinger port
374:            if not all([host, username, password]):
375:                print(f"⚠️  Incomplete SSH credentials for {self.site_key}")
377:                print(f"      - HOSTINGER_HOST: {'✅ Set' if host else '❌ Missing'}")
378:                print(f"      - HOSTINGER_USER: {'✅ Set' if username else '❌ Missing'}")
379:                print(f"      - HOSTINGER_PASS: {'✅ Set' if password else '❌ Missing'}")
382:            ssh = paramiko.SSHClient()
383:            ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
384:            ssh.connect(host, port=port, username=username, password=password, timeout=10)
385:            stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
390:            ssh.close()
397:            print(f"❌ SSH authentication failed for {self.site_key}")
399:            print(f"   Details: Invalid username or password for {username}@{host}:{port}")
401:        except paramiko.SSHException as e:
402:            print(f"❌ SSH connection error for {self.site_key}")
405:            print(f"   Connection: {username}@{host}:{port}")
408:            print(f"❌ SSH command error for {self.site_key}")
412:            print(f"   Connection: {username}@{host}:{port}")
427:        if not self.sftp:
492:        if self.sftp:
493:            self.sftp.close()

## ops/deployment/unified_deployer.py
6:Deploys files to all configured websites using SFTP or REST API.
132:        'southwestsecret.com': {
134:                base_dir / "wordpress-theme" / "southwestsecret",  # Preferred canonical
135:                base_dir / "wp" / "wp-content" / "themes" / "southwestsecret"  # Legacy
277:                    if method == 'sftp':
278:                        remote_path = site_configs[site_domain].get('sftp', {}).get('remote_path', 'N/A')
313:            sftp_base = site_config.get('sftp', {}).get('remote_path', '') or getattr(
318:                if len(parts) > 1 and sftp_base:
319:                    remote_path = f"{sftp_base}/wp-content/{parts[1]}"
320:            elif sftp_base:
325:                    remote_path = f"{sftp_base}/{rel.as_posix()}"
330:                            remote_path = f"{sftp_base}/{tail}"
```

## Decision

- Repair evidence artifacts are safe to preserve as repo history documentation.
- Deployment scripts remain untracked until reviewed in a separate tooling lane.
