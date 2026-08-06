/**
 * @file
 * Shows the color scale popover on hover and focus.
 */

((Drupal, once) => {
  const VIEWPORT_MARGIN = 8;

  /**
   * Positions the popover below its trigger, clamped to the viewport.
   *
   * @param {HTMLElement} trigger
   *   The color-scaled value element.
   * @param {HTMLElement} popover
   *   The popover element.
   */
  const positionPopover = (trigger, popover) => {
    const rect = trigger.getBoundingClientRect();
    const { offsetWidth } = popover;
    let left = rect.left + rect.width / 2 - offsetWidth / 2;
    left = Math.max(
      VIEWPORT_MARGIN,
      Math.min(left, window.innerWidth - offsetWidth - VIEWPORT_MARGIN),
    );
    popover.style.left = `${left}px`;
    popover.style.top = `${rect.bottom + VIEWPORT_MARGIN}px`;
  };

  Drupal.behaviors.vcsScalePopover = {
    attach(context) {
      once('vcs-scale-popover', '[data-vcs-popover]', context).forEach(
        (trigger) => {
          const popover = document.getElementById(trigger.dataset.vcsPopover);
          if (!popover || typeof popover.showPopover !== 'function') {
            return;
          }
          // The title attribute is only the fallback for browsers without
          // the Popover API; drop it here so the native tooltip and the
          // popover never show together.
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
          // Touch devices fire neither hover nor keyboard focus reliably;
          // a tap shows the popover and light dismiss closes it.
          trigger.addEventListener('click', show);
          trigger.addEventListener('mouseleave', hide);
          trigger.addEventListener('focusout', hide);
        },
      );
    },
  };
})(Drupal, once);
