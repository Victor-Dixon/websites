(function (blocks, element, editor) {
  blocks.registerBlockType('swarm-heartbeat/block', {
    title: 'Swarm Heartbeat',
    icon: 'heart',
    category: 'widgets',
    attributes: {
      showBanner: {
        type: 'boolean',
        default: false,
      },
    },
    edit: function () {
      return element.createElement(
        'div',
        { className: 'swarm-heartbeat-block-placeholder' },
        'Swarm Heartbeat preview will render on the front end.'
      );
    },
    save: function () {
      return null;
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.editor);
