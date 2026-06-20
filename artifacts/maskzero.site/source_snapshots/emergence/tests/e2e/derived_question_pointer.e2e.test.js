import test from 'node:test';
import assert from 'node:assert/strict';

function deriveCurrentQuestion(responses, total = 36) {
  const answered = Object.keys(responses).length;

  return Math.min(answered + 1, total);
}

test('derived pointer always matches answered count', () => {
  const responses = {};

  for (let i = 1; i <= 36; i++) {
    responses[i] = 'A';

    assert.equal(
      deriveCurrentQuestion(responses),
      Math.min(i + 1, 36)
    );
  }
});

test('duplicate clicks cannot drift derived pointer', () => {
  const responses = {};

  responses[1] = 'A';
  responses[1] = 'A';
  responses[1] = 'A';

  assert.equal(
    deriveCurrentQuestion(responses),
    2
  );

  assert.equal(
    Object.keys(responses).length,
    1
  );
});
