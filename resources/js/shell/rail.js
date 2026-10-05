/*
 * The icon rail: one button per module; a click opens the module's tiles,
 * and a tile opens its screen as a workspace tab. Arrow keys move between
 * tiles, Escape or a click elsewhere closes the menu.
 */
window.aeRail = function () {
    return {
        openGroup: null,
        mobile: false,

        toggle(key) {
            this.openGroup = this.openGroup === key ? null : key;

            if (this.openGroup) {
                this.$nextTick(() => {
                    var first = this.$root.querySelector('.ae-flyout[data-group="' + key + '"] .ae-tile');

                    if (first) {
                        first.focus();
                    }
                });
            }
        },

        close() {
            this.openGroup = null;
            this.mobile = false;
        },

        // The topbar's menu button sits outside the rail; its own click must not close the sheet it opens.
        closeUnlessToggle(event) {
            if (event && event.target && event.target.closest && event.target.closest('.ae-menu-btn')) {
                return;
            }

            this.close();
        },

        toggleMobile() {
            this.mobile = !this.mobile;

            if (this.mobile && !this.openGroup) {
                var first = this.$root.querySelector('[data-group-btn]');
                this.openGroup = first ? first.dataset.groupBtn : null;
            }
        },

        home() {
            this.close();

            if (window.aeShell) {
                window.aeShell.home();
            }
        },

        openTile(link) {
            var url = new URL(link.href, window.location.href);
            var label = link.querySelector('.ae-tile-label');
            this.close();

            if (window.aeShell) {
                window.aeShell.open(url.pathname + url.search, label ? label.textContent.trim() : '');
            } else {
                window.location.href = link.href;
            }
        },

        moveFocus(event) {
            var keys = { ArrowRight: 1, ArrowLeft: -1, ArrowDown: 'down', ArrowUp: 'up' };

            if (!(event.key in keys)) {
                return;
            }

            var grid = event.currentTarget.querySelector('.ae-tiles');
            var tiles = Array.from(grid.querySelectorAll('.ae-tile'));
            var index = tiles.indexOf(document.activeElement);

            if (index < 0) {
                return;
            }

            var columns = getComputedStyle(grid).gridTemplateColumns.split(' ').length || 1;
            var step = keys[event.key] === 'down' ? columns : keys[event.key] === 'up' ? -columns : keys[event.key];
            var next = tiles[index + step];

            if (next) {
                event.preventDefault();
                next.focus();
            }
        },
    };
};
