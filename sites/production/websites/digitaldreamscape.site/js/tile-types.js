/* Digital Dreamscape — tile definitions */
(function (global) {
  "use strict";

  var TILE_TYPES = {
    grass: {
      id: "grass",
      name: "Dream Grass",
      color: "#5CE08A",
      walkable: true,
      layer: "terrain"
    },
    path: {
      id: "path",
      name: "Dream Path",
      color: "#D4B87A",
      walkable: true,
      layer: "terrain"
    },
    water: {
      id: "water",
      name: "Dream Water",
      color: "#3A9BDC",
      walkable: false,
      layer: "terrain"
    },
    tree: {
      id: "tree",
      name: "Glow Tree",
      color: "#2D7A4F",
      walkable: false,
      layer: "decoration"
    },
    rock: {
      id: "rock",
      name: "Memory Rock",
      color: "#8A8A94",
      walkable: false,
      layer: "decoration"
    },
    crystal: {
      id: "crystal",
      name: "Dream Crystal",
      color: "#00E5FF",
      walkable: false,
      layer: "decoration"
    },
    floor: {
      id: "floor",
      name: "Archive Floor",
      color: "#B8CCE0",
      walkable: true,
      layer: "terrain"
    },
    wall: {
      id: "wall",
      name: "Archive Wall",
      color: "#2A4A6E",
      walkable: false,
      layer: "terrain"
    },
    portal: {
      id: "portal",
      name: "Dream Portal",
      color: "#9B5DE5",
      walkable: true,
      layer: "decoration"
    }
  };

  global.DD_TILE_TYPES = TILE_TYPES;
})(typeof window !== "undefined" ? window : global);
