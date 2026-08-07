((Drupal, once) => {
  const VIEWPORT_MARGIN = 8;

  const positionPopover = (trigger, popover) => {
    const rect = trigger.getBoundingClientRect();
    const { offsetWidth, offsetHeight } = popover;
    let left = rect.left + rect.width / 2 - offsetWidth / 2;
    left = Math.max(
      VIEWPORT_MARGIN,
      Math.min(left, window.innerWidth - offsetWidth - VIEWPORT_MARGIN),
    );
    popover.style.left = `${left}px`;
    const spaceBelow = window.innerHeight - rect.bottom - VIEWPORT_MARGIN;
    popover.style.top = spaceBelow >= offsetHeight
      ? `${rect.bottom + VIEWPORT_MARGIN}px`
      : `${rect.top - offsetHeight - VIEWPORT_MARGIN}px`;
  };

  Drupal.behaviors.vcsScalePopover = {
    attach(context) {
      once('vcs-scale-popover', '[data-vcs-popover]', context).forEach(
        (trigger) => {
          const popover = document.getElementById(trigger.dataset.vcsPopover);
          if (!popover || typeof popover.showPopover !== 'function') {
            return;
          }
          trigger.removeAttribute('title');
          const show = () => {
            if (!popover.matches(':popover-open')) {
              popover.showPopover();
              positionPopover(trigger, popover);
            }
          };
          const hide = () => {
            if (popover.matches(':popover-open')) {
              popover.hidePopover();
            }
          };
          trigger.addEventListener('mouseenter', show);
          trigger.addEventListener('focusin', show);
          trigger.addEventListener('click', show);
          trigger.addEventListener('mouseleave', hide);
          trigger.addEventListener('focusout', hide);
        },
      );
    },
  };
})(Drupal, once);
