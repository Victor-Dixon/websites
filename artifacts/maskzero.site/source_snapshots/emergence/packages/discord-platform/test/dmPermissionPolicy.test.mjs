import assert from "node:assert/strict";
import test from "node:test";

import {
  evaluateDmAction,
  evaluateDmActionPlan
} from "../src/dmPermissionPolicy.mjs";

test("allows scoped campaign channel creation", () => {
  const result = evaluateDmAction({
    type: "create_text_channel",
    name: "campaign-table"
  });

  assert.equal(result.allowed, true);
  assert.equal(result.status, "allowed");
});

test("blocks unknown actions", () => {
  const result = evaluateDmAction({
    type: "grant_admin",
    name: "bad"
  });

  assert.equal(result.allowed, false);
  assert.equal(result.status, "rejected");
});

test("requires approval for destructive channel deletion", () => {
  const result = evaluateDmAction({
    type: "delete_channel",
    name: "campaign-table"
  });

  assert.equal(result.allowed, false);
  assert.equal(result.status, "approval_required");
});

test("requires approval for force character reroll", () => {
  const result = evaluateDmAction({
    type: "force_character_reroll",
    discordUserId: "100000000000002"
  });

  assert.equal(result.allowed, false);
  assert.equal(result.status, "approval_required");
});

test("evaluates whole action plan", () => {
  const result = evaluateDmActionPlan([
    { type: "create_text_channel", name: "safe" },
    { type: "delete_channel", name: "danger" }
  ]);

  assert.equal(result.length, 2);
  assert.equal(result[0].decision.allowed, true);
  assert.equal(result[1].decision.allowed, false);
});
