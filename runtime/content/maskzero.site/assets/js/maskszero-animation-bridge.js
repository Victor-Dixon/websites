(function (root) {
  'use strict';

  var API_BASE = 'https://video-api.maskszero.site';

  function mockCreate(payload) {
    var id = 'mock_' + Date.now().toString(36);
    return {
      ok: true,
      job_id: id,
      status: 'queued',
      status_url: API_BASE + '/v1/jobs/' + id,
      mock: true,
      _payload: payload
    };
  }

  function mockGet(jobId) {
    return {
      ok: true,
      job_id: jobId,
      status: 'complete',
      render_url: null,
      thumbnail_url: null,
      error: null,
      mock: true
    };
  }

  function createJob(payload) {
    return fetch(API_BASE + '/v1/jobs', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) {
            throw new Error((data && data.detail) || 'create_failed');
          }
          return data;
        });
      })
      .catch(function () {
        return mockCreate(payload);
      });
  }

  function getJob(jobId) {
    if (String(jobId).indexOf('mock_') === 0) {
      return Promise.resolve(mockGet(jobId));
    }
    return fetch(API_BASE + '/v1/jobs/' + encodeURIComponent(jobId), {
      method: 'GET',
      headers: { 'Accept': 'application/json' }
    })
      .then(function (response) {
        return response.json().then(function (data) {
          if (!response.ok) {
            throw new Error((data && data.detail) || 'get_failed');
          }
          return data;
        });
      })
      .catch(function () {
        return mockGet(jobId);
      });
  }

  function pollJob(jobId, opts) {
    opts = opts || {};
    var intervalMs = opts.intervalMs || 10000;
    var maxAttempts = opts.maxAttempts || 60;
    var attempt = 0;

    function tick() {
      attempt += 1;
      return getJob(jobId).then(function (job) {
        if (job.status === 'complete' || job.status === 'failed') {
          return job;
        }
        if (attempt >= maxAttempts) {
          job.status = 'failed';
          job.error = job.error || 'poll_timeout';
          return job;
        }
        return new Promise(function (resolve) {
          setTimeout(resolve, intervalMs);
        }).then(tick);
      });
    }

    return tick();
  }

  function battleCardPayload(opts) {
    opts = opts || {};
    return {
      source: 'maskzero.site',
      event_type: opts.event_type || 'battle_card',
      scene_id: opts.scene_id || 'scene_001',
      character_id: opts.character_id || 'mav_zero',
      prompt: opts.prompt || 'Mav Zero enters a neon rooftop battlefield...',
      style: opts.style || 'comic-anime-cinematic',
      duration_seconds: opts.duration_seconds || 5,
      return_type: opts.return_type || 'video'
    };
  }

  root.MasksZeroAnimationBridge = {
    API_BASE: API_BASE,
    createJob: createJob,
    getJob: getJob,
    pollJob: pollJob,
    battleCardPayload: battleCardPayload
  };
})(typeof window !== 'undefined' ? window : this);
