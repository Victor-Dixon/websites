/* Digital Dreamscape — viewport camera */
(function (global) {
  "use strict";

  function createCamera(world, viewportW, viewportH, tileSize) {
    var viewCols = Math.floor(viewportW / tileSize);
    var viewRows = Math.floor(viewportH / tileSize);
    return {
      x: 0,
      y: 0,
      viewCols: viewCols,
      viewRows: viewRows,
      update: function (renderX, renderY) {
        this.x = Math.max(0, Math.min(world.width - viewCols, Math.round(renderX - viewCols / 2)));
        this.y = Math.max(0, Math.min(world.height - viewRows, Math.round(renderY - viewRows / 2)));
      }
    };
  }

  global.DD_CAMERA = {
    createCamera: createCamera
  };
})(typeof window !== "undefined" ? window : global);
