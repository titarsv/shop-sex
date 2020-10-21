// File - wp_includes/js/hoverIntent.js
(function($) {
    $.fn.hoverIntent = function(handlerIn,handlerOut,selector) {

        // default configuration values
        var cfg = {
            interval: 100,
            sensitivity: 6,
            timeout: 0
        };

        if ( typeof handlerIn === "object" ) {
            cfg = $.extend(cfg, handlerIn );
        } else if ($.isFunction(handlerOut)) {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerOut, selector: selector } );
        } else {
            cfg = $.extend(cfg, { over: handlerIn, out: handlerIn, selector: handlerOut } );
        }

        // instantiate variables
        // cX, cY = current X and Y position of mouse, updated by mousemove event
        // pX, pY = previous X and Y position of mouse, set by mouseover and polling interval
        var cX, cY, pX, pY;

        // A private function for getting mouse position
        var track = function(ev) {
            cX = ev.pageX;
            cY = ev.pageY;
        };

        // A private function for comparing current and previous mouse position
        var compare = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            // compare mouse positions to see if they've crossed the threshold
            if ( Math.sqrt( (pX-cX)*(pX-cX) + (pY-cY)*(pY-cY) ) < cfg.sensitivity ) {
                $(ob).off("mousemove.hoverIntent",track);
                // set hoverIntent state to true (so mouseOut can be called)
                ob.hoverIntent_s = true;
                return cfg.over.apply(ob,[ev]);
            } else {
                // set previous coordinates for next time
                pX = cX; pY = cY;
                // use self-calling timeout, guarantees intervals are spaced out properly (avoids JavaScript timer bugs)
                ob.hoverIntent_t = setTimeout( function(){compare(ev, ob);} , cfg.interval );
            }
        };

        // A private function for delaying the mouseOut function
        var delay = function(ev,ob) {
            ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t);
            ob.hoverIntent_s = false;
            return cfg.out.apply(ob,[ev]);
        };

        // A private function for handling mouse 'hovering'
        var handleHover = function(e) {
            // copy objects to be passed into t (required for event object to be passed in IE)
            var ev = $.extend({},e);
            var ob = this;

            // cancel hoverIntent timer if it exists
            if (ob.hoverIntent_t) { ob.hoverIntent_t = clearTimeout(ob.hoverIntent_t); }

            // if e.type === "mouseenter"
            if (e.type === "mouseenter") {
                // set "previous" X and Y position based on initial entry point
                pX = ev.pageX; pY = ev.pageY;
                // update "current" X and Y position based on mousemove
                $(ob).on("mousemove.hoverIntent",track);
                // start polling interval (self-calling timeout) to compare mouse coordinates over time
                if (!ob.hoverIntent_s) { ob.hoverIntent_t = setTimeout( function(){compare(ev,ob);} , cfg.interval );}

                // else e.type == "mouseleave"
            } else {
                // unbind expensive mousemove event
                $(ob).off("mousemove.hoverIntent",track);
                // if hoverIntent state is true, then call the mouseOut function after the specified delay
                if (ob.hoverIntent_s) { ob.hoverIntent_t = setTimeout( function(){delay(ev,ob);} , cfg.timeout );}
            }
        };

        // listen for mouseenter and mouseleave
        return this.on({'mouseenter.hoverIntent':handleHover,'mouseleave.hoverIntent':handleHover}, cfg.selector);
    };
})(jQuery);

// File - wp_admin/js/common.js
/* global setUserSetting, ajaxurl, commonL10n, alert, confirm, pagenow */
var showNotice, adminMenu, columns, validateForm, screenMeta;
( function( $, window, undefined ) {
    var $document = $( document ),
        $window = $( window ),
        $body = $( document.body );

// Removed in 3.3.
// (perhaps) needed for back-compat
    adminMenu = {
        init : function() {},
        fold : function() {},
        restoreMenuState : function() {},
        toggle : function() {},
        favorites : function() {}
    };

// show/hide/save table columns
    columns = {
        init : function() {
            var that = this;
            $('.hide-column-tog', '#adv-settings').click( function() {
                var $t = $(this), column = $t.val();
                if ( $t.prop('checked') )
                    that.checked(column);
                else
                    that.unchecked(column);

                columns.saveManageColumnsState();
            });
        },

        saveManageColumnsState : function() {
            var hidden = this.hidden();
            $.post(ajaxurl, {
                action: 'hidden-columns',
                hidden: hidden,
                screenoptionnonce: $('#screenoptionnonce').val(),
                page: pagenow
            });
        },

        checked : function(column) {
            $('.column-' + column).removeClass( 'hidden' );
            this.colSpanChange(+1);
        },

        unchecked : function(column) {
            $('.column-' + column).addClass( 'hidden' );
            this.colSpanChange(-1);
        },

        hidden : function() {
            return $( '.manage-column[id]' ).filter( ':hidden' ).map(function() {
                return this.id;
            }).get().join( ',' );
        },

        useCheckboxesForHidden : function() {
            this.hidden = function(){
                return $('.hide-column-tog').not(':checked').map(function() {
                    var id = this.id;
                    return id.substring( id, id.length - 5 );
                }).get().join(',');
            };
        },

        colSpanChange : function(diff) {
            var $t = $('table').find('.colspanchange'), n;
            if ( !$t.length )
                return;
            n = parseInt( $t.attr('colspan'), 10 ) + diff;
            $t.attr('colspan', n.toString());
        }
    };

    $document.ready(function(){columns.init();});

    validateForm = function( form ) {
        return !$( form )
            .find( '.form-required' )
            .filter( function() { return $( ':input:visible', this ).val() === ''; } )
            .addClass( 'form-invalid' )
            .find( ':input:visible' )
            .change( function() { $( this ).closest( '.form-invalid' ).removeClass( 'form-invalid' ); } )
            .length;
    };

// stub for doing better warnings
    showNotice = {
        warn : function() {
            var msg = commonL10n.warnDelete || '';
            if ( confirm(msg) ) {
                return true;
            }

            return false;
        },

        note : function(text) {
            alert(text);
        }
    };

    screenMeta = {
        element: null, // #screen-meta
        toggles: null, // .screen-meta-toggle
        page:    null, // #wpcontent

        init: function() {
            this.element = $('#screen-meta');
            this.toggles = $( '#screen-meta-links' ).find( '.show-settings' );
            this.page    = $('#wpcontent');

            this.toggles.click( this.toggleEvent );
        },

        toggleEvent: function() {
            var panel = $( '#' + $( this ).attr( 'aria-controls' ) );

            if ( !panel.length )
                return;

            if ( panel.is(':visible') )
                screenMeta.close( panel, $(this) );
            else
                screenMeta.open( panel, $(this) );
        },

        open: function( panel, button ) {

            $( '#screen-meta-links' ).find( '.screen-meta-toggle' ).not( button.parent() ).css( 'visibility', 'hidden' );

            panel.parent().show();
            panel.slideDown( 'fast', function() {
                panel.focus();
                button.addClass( 'screen-meta-active' ).attr( 'aria-expanded', true );
            });

            $document.trigger( 'screen:options:open' );
        },

        close: function( panel, button ) {
            panel.slideUp( 'fast', function() {
                button.removeClass( 'screen-meta-active' ).attr( 'aria-expanded', false );
                $('.screen-meta-toggle').css('visibility', '');
                panel.parent().hide();
            });

            $document.trigger( 'screen:options:close' );
        }
    };

    /**
     * Help tabs.
     */
    $('.contextual-help-tabs').delegate('a', 'click', function(e) {
        var link = $(this),
            panel;

        e.preventDefault();

        // Don't do anything if the click is for the tab already showing.
        if ( link.is('.active a') )
            return false;

        // Links
        $('.contextual-help-tabs .active').removeClass('active');
        link.parent('li').addClass('active');

        panel = $( link.attr('href') );

        // Panels
        $('.help-tab-content').not( panel ).removeClass('active').hide();
        panel.addClass('active').show();
    });

    /**
     * Update custom permalink structure via buttons.
     */

    var permalinkStructureFocused = false,
        $permalinkStructure       = $( '#permalink_structure' ),
        $permalinkStructureInputs = $( '.permalink-structure input:radio' ),
        $permalinkCustomSelection = $( '#custom_selection' ),
        $availableStructureTags   = $( '.form-table.permalink-structure .available-structure-tags button' );

// Change permalink structure input when selecting one of the common structures.
    $permalinkStructureInputs.on( 'change', function() {
        if ( 'custom' === this.value ) {
            return;
        }

        $permalinkStructure.val( this.value );

        // Update button states after selection.
        $availableStructureTags.each( function() {
            changeStructureTagButtonState( $( this ) );
        } );
    } );

    $permalinkStructure.on( 'click input', function() {
        $permalinkCustomSelection.prop( 'checked', true );
    } );

// Check if the permalink structure input field has had focus at least once.
    $permalinkStructure.on( 'focus', function( event ) {
        permalinkStructureFocused = true;
        $( this ).off( event );
    } );

    /**
     * Enables or disables a structure tag button depending on its usage.
     *
     * If the structure is already used in the custom permalink structure,
     * it will be disabled.
     *
     * @param {object} button Button jQuery object.
     */
    function changeStructureTagButtonState( button ) {
        if ( -1 !== $permalinkStructure.val().indexOf( button.text().trim() ) ) {
            button.attr( 'data-label', button.attr( 'aria-label' ) );
            button.attr( 'aria-label', button.attr( 'data-used' ) );
            button.attr( 'aria-pressed', true );
            button.addClass( 'active' );
        } else if ( button.attr( 'data-label' ) ) {
            button.attr( 'aria-label', button.attr( 'data-label' ) );
            button.attr( 'aria-pressed', false );
            button.removeClass( 'active' );
        }
    }

// Check initial button state.
    $availableStructureTags.each( function() {
        changeStructureTagButtonState( $( this ) );
    } );

// Observe permalink structure field and disable buttons of tags that are already present.
    $permalinkStructure.on( 'change', function() {
        $availableStructureTags.each( function() {
            changeStructureTagButtonState( $( this ) );
        } );
    } );

    $availableStructureTags.on( 'click', function() {
        var permalinkStructureValue = $permalinkStructure.val(),
            selectionStart          = $permalinkStructure[ 0 ].selectionStart,
            selectionEnd            = $permalinkStructure[ 0 ].selectionEnd,
            textToAppend            = $( this ).text().trim(),
            textToAnnounce          = $( this ).attr( 'data-added' ),
            newSelectionStart;

        // Remove structure tag if already part of the structure.
        if ( -1 !== permalinkStructureValue.indexOf( textToAppend ) ) {
            permalinkStructureValue = permalinkStructureValue.replace( textToAppend + '/', '' );

            $permalinkStructure.val( '/' === permalinkStructureValue ? '' : permalinkStructureValue );

            // Announce change to screen readers.
            $( '#custom_selection_updated' ).text( textToAnnounce );

            // Disable button.
            changeStructureTagButtonState( $( this ) );

            return;
        }

        // Input field never had focus, move selection to end of input.
        if ( ! permalinkStructureFocused && 0 === selectionStart && 0 === selectionEnd ) {
            selectionStart = selectionEnd = permalinkStructureValue.length;
        }

        $permalinkCustomSelection.prop( 'checked', true );

        // Prepend and append slashes if necessary.
        if ( '/' !== permalinkStructureValue.substr( 0, selectionStart ).substr( -1 ) ) {
            textToAppend = '/' + textToAppend;
        }

        if ( '/' !== permalinkStructureValue.substr( selectionEnd, 1 ) ) {
            textToAppend = textToAppend + '/';
        }

        // Insert structure tag at the specified position.
        $permalinkStructure.val( permalinkStructureValue.substr( 0, selectionStart ) + textToAppend + permalinkStructureValue.substr( selectionEnd ) );

        // Announce change to screen readers.
        $( '#custom_selection_updated' ).text( textToAnnounce );

        // Disable button.
        changeStructureTagButtonState( $( this ) );

        // If input had focus give it back with cursor right after appended text.
        if ( permalinkStructureFocused && $permalinkStructure[0].setSelectionRange ) {
            newSelectionStart = ( permalinkStructureValue.substr( 0, selectionStart ) + textToAppend ).length;
            $permalinkStructure[0].setSelectionRange( newSelectionStart, newSelectionStart );
            $permalinkStructure.focus();
        }
    } );

    $document.ready( function() {
        var checks, first, last, checked, sliced, mobileEvent, transitionTimeout, focusedRowActions,
            lastClicked = false,
            pageInput = $('input.current-page'),
            currentPage = pageInput.val(),
            isIOS = /iPhone|iPad|iPod/.test( navigator.userAgent ),
            isAndroid = navigator.userAgent.indexOf( 'Android' ) !== -1,
            isIE8 = $( document.documentElement ).hasClass( 'ie8' ),
            $adminMenuWrap = $( '#adminmenuwrap' ),
            $wpwrap = $( '#wpwrap' ),
            $adminmenu = $( '#adminmenu' ),
            $overlay = $( '#wp-responsive-overlay' ),
            $toolbar = $( '#wp-toolbar' ),
            $toolbarPopups = $toolbar.find( 'a[aria-haspopup="true"]' ),
            $sortables = $('.meta-box-sortables'),
            wpResponsiveActive = false,
            $adminbar = $( '#wpadminbar' ),
            lastScrollPosition = 0,
            pinnedMenuTop = false,
            pinnedMenuBottom = false,
            menuTop = 0,
            menuState,
            menuIsPinned = false,
            height = {
                window: $window.height(),
                wpwrap: $wpwrap.height(),
                adminbar: $adminbar.height(),
                menu: $adminMenuWrap.height()
            },
            $headerEnd = $( '.wp-header-end' );


        // when the menu is folded, make the fly-out submenu header clickable
        $adminmenu.on('click.wp-submenu-head', '.wp-submenu-head', function(e){
            $(e.target).parent().siblings('a').get(0).click();
        });

        $( '#collapse-button' ).on( 'click.collapse-menu', function() {
            var viewportWidth = getViewportWidth() || 961;

            // reset any compensation for submenus near the bottom of the screen
            $('#adminmenu div.wp-submenu').css('margin-top', '');

            if ( viewportWidth < 960 ) {
                if ( $body.hasClass('auto-fold') ) {
                    $body.removeClass('auto-fold').removeClass('folded');
                    setUserSetting('unfold', 1);
                    setUserSetting('mfold', 'o');
                    menuState = 'open';
                } else {
                    $body.addClass('auto-fold');
                    setUserSetting('unfold', 0);
                    menuState = 'folded';
                }
            } else {
                if ( $body.hasClass('folded') ) {
                    $body.removeClass('folded');
                    setUserSetting('mfold', 'o');
                    menuState = 'open';
                } else {
                    $body.addClass('folded');
                    setUserSetting('mfold', 'f');
                    menuState = 'folded';
                }
            }

            $document.trigger( 'wp-collapse-menu', { state: menuState } );
        });

        // Handle the `aria-haspopup` attribute on the current menu item when it has a sub-menu.
        function currentMenuItemHasPopup() {
            var $current = $( 'a.wp-has-current-submenu' );

            if ( 'folded' === menuState ) {
                // When folded or auto-folded and not responsive view, the current menu item does have a fly-out sub-menu.
                $current.attr( 'aria-haspopup', 'true' );
            } else {
                // When expanded or in responsive view, reset aria-haspopup.
                $current.attr( 'aria-haspopup', 'false' );
            }
        }

        $document.on( 'wp-menu-state-set wp-collapse-menu wp-responsive-activate wp-responsive-deactivate', currentMenuItemHasPopup );

        /**
         * Ensure an admin submenu is within the visual viewport.
         *
         * @since 4.1.0
         *
         * @param {jQuery} $menuItem The parent menu item containing the submenu.
         */
        function adjustSubmenu( $menuItem ) {
            var bottomOffset, pageHeight, adjustment, theFold, menutop, wintop, maxtop,
                $submenu = $menuItem.find( '.wp-submenu' );

            menutop = $menuItem.offset().top;
            wintop = $window.scrollTop();
            maxtop = menutop - wintop - 30; // max = make the top of the sub almost touch admin bar

            bottomOffset = menutop + $submenu.height() + 1; // Bottom offset of the menu
            pageHeight = $wpwrap.height(); // Height of the entire page
            adjustment = 60 + bottomOffset - pageHeight;
            theFold = $window.height() + wintop - 50; // The fold

            if ( theFold < ( bottomOffset - adjustment ) ) {
                adjustment = bottomOffset - theFold;
            }

            if ( adjustment > maxtop ) {
                adjustment = maxtop;
            }

            if ( adjustment > 1 ) {
                $submenu.css( 'margin-top', '-' + adjustment + 'px' );
            } else {
                $submenu.css( 'margin-top', '' );
            }
        }

        if ( 'ontouchstart' in window || /IEMobile\/[1-9]/.test(navigator.userAgent) ) { // touch screen device
            // iOS Safari works with touchstart, the rest work with click
            mobileEvent = isIOS ? 'touchstart' : 'click';

            // close any open submenus when touch/click is not on the menu
            $body.on( mobileEvent+'.wp-mobile-hover', function(e) {
                if ( $adminmenu.data('wp-responsive') ) {
                    return;
                }

                if ( ! $( e.target ).closest( '#adminmenu' ).length ) {
                    $adminmenu.find( 'li.opensub' ).removeClass( 'opensub' );
                }
            });

            $adminmenu.find( 'a.wp-has-submenu' ).on( mobileEvent + '.wp-mobile-hover', function( event ) {
                var $menuItem = $(this).parent();

                if ( $adminmenu.data( 'wp-responsive' ) ) {
                    return;
                }

                // Show the sub instead of following the link if:
                //	- the submenu is not open
                //	- the submenu is not shown inline or the menu is not folded
                if ( ! $menuItem.hasClass( 'opensub' ) && ( ! $menuItem.hasClass( 'wp-menu-open' ) || $menuItem.width() < 40 ) ) {
                    event.preventDefault();
                    adjustSubmenu( $menuItem );
                    $adminmenu.find( 'li.opensub' ).removeClass( 'opensub' );
                    $menuItem.addClass('opensub');
                }
            });
        }

        if ( ! isIOS && ! isAndroid ) {
            $adminmenu.find( 'li.wp-has-submenu' ).hoverIntent({
                over: function() {
                    var $menuItem = $( this ),
                        $submenu = $menuItem.find( '.wp-submenu' ),
                        top = parseInt( $submenu.css( 'top' ), 10 );

                    if ( isNaN( top ) || top > -5 ) { // the submenu is visible
                        return;
                    }

                    if ( $adminmenu.data( 'wp-responsive' ) ) {
                        // The menu is in responsive mode, bail
                        return;
                    }

                    adjustSubmenu( $menuItem );
                    $adminmenu.find( 'li.opensub' ).removeClass( 'opensub' );
                    $menuItem.addClass( 'opensub' );
                },
                out: function(){
                    if ( $adminmenu.data( 'wp-responsive' ) ) {
                        // The menu is in responsive mode, bail
                        return;
                    }

                    $( this ).removeClass( 'opensub' ).find( '.wp-submenu' ).css( 'margin-top', '' );
                },
                timeout: 200,
                sensitivity: 7,
                interval: 90
            });

            $adminmenu.on( 'focus.adminmenu', '.wp-submenu a', function( event ) {
                if ( $adminmenu.data( 'wp-responsive' ) ) {
                    // The menu is in responsive mode, bail
                    return;
                }

                $( event.target ).closest( 'li.menu-top' ).addClass( 'opensub' );
            }).on( 'blur.adminmenu', '.wp-submenu a', function( event ) {
                if ( $adminmenu.data( 'wp-responsive' ) ) {
                    return;
                }

                $( event.target ).closest( 'li.menu-top' ).removeClass( 'opensub' );
            }).find( 'li.wp-has-submenu.wp-not-current-submenu' ).on( 'focusin.adminmenu', function() {
                adjustSubmenu( $( this ) );
            });
        }

        /*
         * The `.below-h2` class is here just for backward compatibility with plugins
         * that are (incorrectly) using it. Do not use. Use `.inline` instead. See #34570.
         * If '.wp-header-end' is found, append the notices after it otherwise
         * after the first h1 or h2 heading found within the main content.
         */
        if ( ! $headerEnd.length ) {
            $headerEnd = $( '.wrap h1, .wrap h2' ).first();
        }
        $( 'div.updated, div.error, div.notice' ).not( '.inline, .below-h2' ).insertAfter( $headerEnd );

        // Make notices dismissible
        function makeNoticesDismissible() {
            $( '.notice.is-dismissible' ).each( function() {
                var $el = $( this ),
                    $button = $( '<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>' ),
                    btnText = commonL10n.dismiss || '';

                // Ensure plain text
                $button.find( '.screen-reader-text' ).text( btnText );
                $button.on( 'click.wp-dismiss-notice', function( event ) {
                    event.preventDefault();
                    $el.fadeTo( 100, 0, function() {
                        $el.slideUp( 100, function() {
                            $el.remove();
                        });
                    });
                });

                $el.append( $button );
            });
        }

        $document.on( 'wp-updates-notice-added wp-plugin-install-error wp-plugin-update-error wp-plugin-delete-error wp-theme-install-error wp-theme-delete-error', makeNoticesDismissible );

        // Init screen meta
        screenMeta.init();

        // This event needs to be delegated. Ticket #37973.
        $body.on( 'click', 'tbody > tr > .check-column :checkbox', function( event ) {
            // Shift click to select a range of checkboxes.
            if ( 'undefined' == event.shiftKey ) { return true; }
            if ( event.shiftKey ) {
                if ( !lastClicked ) { return true; }
                checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' ).filter( ':visible:enabled' );
                first = checks.index( lastClicked );
                last = checks.index( this );
                checked = $(this).prop('checked');
                if ( 0 < first && 0 < last && first != last ) {
                    sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
                    sliced.prop( 'checked', function() {
                        if ( $(this).closest('tr').is(':visible') )
                            return checked;

                        return false;
                    });
                }
            }
            lastClicked = this;

            // Toggle the "Select all" checkboxes depending if the other ones are all checked or not.
            var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible:enabled').not(':checked');
            $(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
                return ( 0 === unchecked.length );
            });

            return true;
        });

        // This event needs to be delegated. Ticket #37973.
        $body.on( 'click.wp-toggle-checkboxes', 'thead .check-column :checkbox, tfoot .check-column :checkbox', function( event ) {
            var $this = $(this),
                $table = $this.closest( 'table' ),
                controlChecked = $this.prop('checked'),
                toggle = event.shiftKey || $this.data('wp-toggle');

            $table.children( 'tbody' ).filter(':visible')
                .children().children('.check-column').find(':checkbox')
                .prop('checked', function() {
                    if ( $(this).is(':hidden,:disabled') ) {
                        return false;
                    }

                    if ( toggle ) {
                        return ! $(this).prop( 'checked' );
                    } else if ( controlChecked ) {
                        return true;
                    }

                    return false;
                });

            $table.children('thead,  tfoot').filter(':visible')
                .children().children('.check-column').find(':checkbox')
                .prop('checked', function() {
                    if ( toggle ) {
                        return false;
                    } else if ( controlChecked ) {
                        return true;
                    }

                    return false;
                });
        });

        // Show row actions on keyboard focus of its parent container element or any other elements contained within
        $( '#wpbody-content' ).on({
            focusin: function() {
                clearTimeout( transitionTimeout );
                focusedRowActions = $( this ).find( '.row-actions' );
                // transitionTimeout is necessary for Firefox, but Chrome won't remove the CSS class without a little help.
                $( '.row-actions' ).not( this ).removeClass( 'visible' );
                focusedRowActions.addClass( 'visible' );
            },
            focusout: function() {
                // Tabbing between post title and .row-actions links needs a brief pause, otherwise
                // the .row-actions div gets hidden in transit in some browsers (ahem, Firefox).
                transitionTimeout = setTimeout( function() {
                    focusedRowActions.removeClass( 'visible' );
                }, 30 );
            }
        }, '.has-row-actions' );

        // Toggle list table rows on small screens
        $( 'tbody' ).on( 'click', '.toggle-row', function() {
            $( this ).closest( 'tr' ).toggleClass( 'is-expanded' );
        });

        $('#default-password-nag-no').click( function() {
            setUserSetting('default_password_nag', 'hide');
            $('div.default-password-nag').hide();
            return false;
        });

        // tab in textareas
        $('#newcontent').bind('keydown.wpevent_InsertTab', function(e) {
            var el = e.target, selStart, selEnd, val, scroll, sel;

            if ( e.keyCode == 27 ) { // escape key
                // when pressing Escape: Opera 12 and 27 blur form fields, IE 8 clears them
                e.preventDefault();
                $(el).data('tab-out', true);
                return;
            }

            if ( e.keyCode != 9 || e.ctrlKey || e.altKey || e.shiftKey ) // tab key
                return;

            if ( $(el).data('tab-out') ) {
                $(el).data('tab-out', false);
                return;
            }

            selStart = el.selectionStart;
            selEnd = el.selectionEnd;
            val = el.value;

            if ( document.selection ) {
                el.focus();
                sel = document.selection.createRange();
                sel.text = '\t';
            } else if ( selStart >= 0 ) {
                scroll = this.scrollTop;
                el.value = val.substring(0, selStart).concat('\t', val.substring(selEnd) );
                el.selectionStart = el.selectionEnd = selStart + 1;
                this.scrollTop = scroll;
            }

            if ( e.stopPropagation )
                e.stopPropagation();
            if ( e.preventDefault )
                e.preventDefault();
        });

        if ( pageInput.length ) {
            pageInput.closest('form').submit( function() {

                // Reset paging var for new filters/searches but not for bulk actions. See #17685.
                if ( $('select[name="action"]').val() == -1 && $('select[name="action2"]').val() == -1 && pageInput.val() == currentPage )
                    pageInput.val('1');
            });
        }

        $('.search-box input[type="search"], .search-box input[type="submit"]').mousedown(function () {
            $('select[name^="action"]').val('-1');
        });

        // Scroll into view when focused
        $('#contextual-help-link, #show-settings-link').on( 'focus.scroll-into-view', function(e){
            if ( e.target.scrollIntoView )
                e.target.scrollIntoView(false);
        });

        // Disable upload buttons until files are selected
        (function(){
            var button, input, form = $('form.wp-upload-form');
            if ( ! form.length )
                return;
            button = form.find('input[type="submit"]');
            input = form.find('input[type="file"]');

            function toggleUploadButton() {
                button.prop('disabled', '' === input.map( function() {
                    return $(this).val();
                }).get().join(''));
            }
            toggleUploadButton();
            input.on('change', toggleUploadButton);
        })();

        function pinMenu( event ) {
            var windowPos = $window.scrollTop(),
                resizing = ! event || event.type !== 'scroll';

            if ( isIOS || isIE8 || $adminmenu.data( 'wp-responsive' ) ) {
                return;
            }

            if ( height.menu + height.adminbar < height.window ||
                height.menu + height.adminbar + 20 > height.wpwrap ) {
                unpinMenu();
                return;
            }

            menuIsPinned = true;

            if ( height.menu + height.adminbar > height.window ) {
                // Check for overscrolling
                if ( windowPos < 0 ) {
                    if ( ! pinnedMenuTop ) {
                        pinnedMenuTop = true;
                        pinnedMenuBottom = false;

                        $adminMenuWrap.css({
                            position: 'fixed',
                            top: '',
                            bottom: ''
                        });
                    }

                    return;
                } else if ( windowPos + height.window > $document.height() - 1 ) {
                    if ( ! pinnedMenuBottom ) {
                        pinnedMenuBottom = true;
                        pinnedMenuTop = false;

                        $adminMenuWrap.css({
                            position: 'fixed',
                            top: '',
                            bottom: 0
                        });
                    }

                    return;
                }

                if ( windowPos > lastScrollPosition ) {
                    // Scrolling down
                    if ( pinnedMenuTop ) {
                        // let it scroll
                        pinnedMenuTop = false;
                        menuTop = $adminMenuWrap.offset().top - height.adminbar - ( windowPos - lastScrollPosition );

                        if ( menuTop + height.menu + height.adminbar < windowPos + height.window ) {
                            menuTop = windowPos + height.window - height.menu - height.adminbar;
                        }

                        $adminMenuWrap.css({
                            position: 'absolute',
                            top: menuTop,
                            bottom: ''
                        });
                    } else if ( ! pinnedMenuBottom && $adminMenuWrap.offset().top + height.menu < windowPos + height.window ) {
                        // pin the bottom
                        pinnedMenuBottom = true;

                        $adminMenuWrap.css({
                            position: 'fixed',
                            top: '',
                            bottom: 0
                        });
                    }
                } else if ( windowPos < lastScrollPosition ) {
                    // Scrolling up
                    if ( pinnedMenuBottom ) {
                        // let it scroll
                        pinnedMenuBottom = false;
                        menuTop = $adminMenuWrap.offset().top - height.adminbar + ( lastScrollPosition - windowPos );

                        if ( menuTop + height.menu > windowPos + height.window ) {
                            menuTop = windowPos;
                        }

                        $adminMenuWrap.css({
                            position: 'absolute',
                            top: menuTop,
                            bottom: ''
                        });
                    } else if ( ! pinnedMenuTop && $adminMenuWrap.offset().top >= windowPos + height.adminbar ) {
                        // pin the top
                        pinnedMenuTop = true;

                        $adminMenuWrap.css({
                            position: 'fixed',
                            top: '',
                            bottom: ''
                        });
                    }
                } else if ( resizing ) {
                    // Resizing
                    pinnedMenuTop = pinnedMenuBottom = false;
                    menuTop = windowPos + height.window - height.menu - height.adminbar - 1;

                    if ( menuTop > 0 ) {
                        $adminMenuWrap.css({
                            position: 'absolute',
                            top: menuTop,
                            bottom: ''
                        });
                    } else {
                        unpinMenu();
                    }
                }
            }

            lastScrollPosition = windowPos;
        }

        function resetHeights() {
            height = {
                window: $window.height(),
                wpwrap: $wpwrap.height(),
                adminbar: $adminbar.height(),
                menu: $adminMenuWrap.height()
            };
        }

        function unpinMenu() {
            if ( isIOS || ! menuIsPinned ) {
                return;
            }

            pinnedMenuTop = pinnedMenuBottom = menuIsPinned = false;
            $adminMenuWrap.css({
                position: '',
                top: '',
                bottom: ''
            });
        }

        function setPinMenu() {
            resetHeights();

            if ( $adminmenu.data('wp-responsive') ) {
                $body.removeClass( 'sticky-menu' );
                unpinMenu();
            } else if ( height.menu + height.adminbar > height.window ) {
                pinMenu();
                $body.removeClass( 'sticky-menu' );
            } else {
                $body.addClass( 'sticky-menu' );
                unpinMenu();
            }
        }

        if ( ! isIOS ) {
            $window.on( 'scroll.pin-menu', pinMenu );
            $document.on( 'tinymce-editor-init.pin-menu', function( event, editor ) {
                editor.on( 'wp-autoresize', resetHeights );
            });
        }

        window.wpResponsive = {
            init: function() {
                var self = this;

                // Modify functionality based on custom activate/deactivate event
                $document.on( 'wp-responsive-activate.wp-responsive', function() {
                    self.activate();
                }).on( 'wp-responsive-deactivate.wp-responsive', function() {
                    self.deactivate();
                });

                $( '#wp-admin-bar-menu-toggle a' ).attr( 'aria-expanded', 'false' );

                // Toggle sidebar when toggle is clicked
                $( '#wp-admin-bar-menu-toggle' ).on( 'click.wp-responsive', function( event ) {
                    event.preventDefault();

                    // close any open toolbar submenus
                    $adminbar.find( '.hover' ).removeClass( 'hover' );

                    $wpwrap.toggleClass( 'wp-responsive-open' );
                    if ( $wpwrap.hasClass( 'wp-responsive-open' ) ) {
                        $(this).find('a').attr( 'aria-expanded', 'true' );
                        $( '#adminmenu a:first' ).focus();
                    } else {
                        $(this).find('a').attr( 'aria-expanded', 'false' );
                    }
                } );

                // Add menu events
                $adminmenu.on( 'click.wp-responsive', 'li.wp-has-submenu > a', function( event ) {
                    if ( ! $adminmenu.data('wp-responsive') ) {
                        return;
                    }

                    $( this ).parent( 'li' ).toggleClass( 'selected' );
                    event.preventDefault();
                });

                self.trigger();
                $document.on( 'wp-window-resized.wp-responsive', $.proxy( this.trigger, this ) );

                // This needs to run later as UI Sortable may be initialized later on $(document).ready()
                $window.on( 'load.wp-responsive', function() {
                    var width = navigator.userAgent.indexOf('AppleWebKit/') > -1 ? $window.width() : window.innerWidth;

                    if ( width <= 782 ) {
                        self.disableSortables();
                    }
                });
            },

            activate: function() {
                setPinMenu();

                if ( ! $body.hasClass( 'auto-fold' ) ) {
                    $body.addClass( 'auto-fold' );
                }

                $adminmenu.data( 'wp-responsive', 1 );
                this.disableSortables();
            },

            deactivate: function() {
                setPinMenu();
                $adminmenu.removeData('wp-responsive');
                this.enableSortables();
            },

            trigger: function() {
                var viewportWidth = getViewportWidth();

                // Exclude IE < 9, it doesn't support @media CSS rules.
                if ( ! viewportWidth ) {
                    return;
                }

                if ( viewportWidth <= 782 ) {
                    if ( ! wpResponsiveActive ) {
                        $document.trigger( 'wp-responsive-activate' );
                        wpResponsiveActive = true;
                    }
                } else {
                    if ( wpResponsiveActive ) {
                        $document.trigger( 'wp-responsive-deactivate' );
                        wpResponsiveActive = false;
                    }
                }

                if ( viewportWidth <= 480 ) {
                    this.enableOverlay();
                } else {
                    this.disableOverlay();
                }
            },

            enableOverlay: function() {
                if ( $overlay.length === 0 ) {
                    $overlay = $( '<div id="wp-responsive-overlay"></div>' )
                        .insertAfter( '#wpcontent' )
                        .hide()
                        .on( 'click.wp-responsive', function() {
                            $toolbar.find( '.menupop.hover' ).removeClass( 'hover' );
                            $( this ).hide();
                        });
                }

                $toolbarPopups.on( 'click.wp-responsive', function() {
                    $overlay.show();
                });
            },

            disableOverlay: function() {
                $toolbarPopups.off( 'click.wp-responsive' );
                $overlay.hide();
            },

            disableSortables: function() {
                if ( $sortables.length ) {
                    try {
                        $sortables.sortable('disable');
                    } catch(e) {}
                }
            },

            enableSortables: function() {
                if ( $sortables.length ) {
                    try {
                        $sortables.sortable('enable');
                    } catch(e) {}
                }
            }
        };

        // Add an ARIA role `button` to elements that behave like UI controls when JavaScript is on.
        function aria_button_if_js() {
            $( '.aria-button-if-js' ).attr( 'role', 'button' );
        }

        $( document ).ajaxComplete( function() {
            aria_button_if_js();
        });

        /**
         * @summary Get the viewport width.
         *
         * @since 4.7.0
         *
         * @returns {number|boolean} The current viewport width or false if the
         *                           browser doesn't support innerWidth (IE < 9).
         */
        function getViewportWidth() {
            var viewportWidth = false;

            if ( window.innerWidth ) {
                // On phones, window.innerWidth is affected by zooming.
                viewportWidth = Math.max( window.innerWidth, document.documentElement.clientWidth );
            }

            return viewportWidth;
        }

        /**
         * @summary Set the admin menu collapsed/expanded state.
         *
         * Sets the global variable `menuState` and triggers a custom event passing
         * the current menu state.
         *
         * @since 4.7.0
         *
         * @returns {void}
         */
        function setMenuState() {
            var viewportWidth = getViewportWidth() || 961;

            if ( viewportWidth <= 782  ) {
                menuState = 'responsive';
            } else if ( $body.hasClass( 'folded' ) || ( $body.hasClass( 'auto-fold' ) && viewportWidth <= 960 && viewportWidth > 782 ) ) {
                menuState = 'folded';
            } else {
                menuState = 'open';
            }

            $document.trigger( 'wp-menu-state-set', { state: menuState } );
        }

        // Set the menu state when the window gets resized.
        $document.on( 'wp-window-resized.set-menu-state', setMenuState );

        /**
         * @summary Set ARIA attributes on the collapse/expand menu button.
         *
         * When the admin menu is open or folded, updates the `aria-expanded` and
         * `aria-label` attributes of the button to give feedback to assistive
         * technologies. In the responsive view, the button is always hidden.
         *
         * @since 4.7.0
         *
         * @returns {void}
         */
        $document.on( 'wp-menu-state-set wp-collapse-menu', function( event, eventData ) {
            var $collapseButton = $( '#collapse-button' ),
                ariaExpanded = 'true',
                ariaLabelText = commonL10n.collapseMenu;

            if ( 'folded' === eventData.state ) {
                ariaExpanded = 'false';
                ariaLabelText = commonL10n.expandMenu;
            }

            $collapseButton.attr({
                'aria-expanded': ariaExpanded,
                'aria-label': ariaLabelText
            });
        });

        window.wpResponsive.init();
        setPinMenu();
        setMenuState();
        currentMenuItemHasPopup();
        makeNoticesDismissible();
        aria_button_if_js();

        $document.on( 'wp-pin-menu wp-window-resized.pin-menu postboxes-columnchange.pin-menu postbox-toggled.pin-menu wp-collapse-menu.pin-menu wp-scroll-start.pin-menu', setPinMenu );

        // Set initial focus on a specific element.
        $( '.wp-initial-focus' ).focus();

        // Toggle update details on update-core.php.
        $body.on( 'click', '.js-update-details-toggle', function() {
            var $updateNotice = $( this ).closest( '.js-update-details' ),
                $progressDiv = $( '#' + $updateNotice.data( 'update-details' ) );

            /*
             * When clicking on "Show details" move the progress div below the update
             * notice. Make sure it gets moved just the first time.
             */
            if ( ! $progressDiv.hasClass( 'update-details-moved' ) ) {
                $progressDiv.insertAfter( $updateNotice ).addClass( 'update-details-moved' );
            }

            // Toggle the progress div visibility.
            $progressDiv.toggle();
            // Toggle the Show Details button expanded state.
            $( this ).attr( 'aria-expanded', $progressDiv.is( ':visible' ) );
        });
    });

// Fire a custom jQuery event at the end of window resize
    ( function() {
        var timeout;

        function triggerEvent() {
            $document.trigger( 'wp-window-resized' );
        }

        function fireOnce() {
            window.clearTimeout( timeout );
            timeout = window.setTimeout( triggerEvent, 200 );
        }

        $window.on( 'resize.wp-fire-once', fireOnce );
    }());

// Make Windows 8 devices play along nicely.
    (function(){
        if ( '-ms-user-select' in document.documentElement.style && navigator.userAgent.match(/IEMobile\/10\.0/) ) {
            var msViewportStyle = document.createElement( 'style' );
            msViewportStyle.appendChild(
                document.createTextNode( '@-ms-viewport{width:auto!important}' )
            );
            document.getElementsByTagName( 'head' )[0].appendChild( msViewportStyle );
        }
    })();

}( jQuery, window ));

// File - wp-includes/js/admin-bar.js
/* jshint loopfunc: true */
// use jQuery and hoverIntent if loaded
if ( typeof(jQuery) != 'undefined' ) {
    if ( typeof(jQuery.fn.hoverIntent) == 'undefined' ) {
        /* jshint ignore:start */
        // hoverIntent v1.8.1 - Copy of wp-includes/js/hoverIntent.min.js
        !function(a){a.fn.hoverIntent=function(b,c,d){var e={interval:100,sensitivity:6,timeout:0};e="object"==typeof b?a.extend(e,b):a.isFunction(c)?a.extend(e,{over:b,out:c,selector:d}):a.extend(e,{over:b,out:b,selector:c});var f,g,h,i,j=function(a){f=a.pageX,g=a.pageY},k=function(b,c){return c.hoverIntent_t=clearTimeout(c.hoverIntent_t),Math.sqrt((h-f)*(h-f)+(i-g)*(i-g))<e.sensitivity?(a(c).off("mousemove.hoverIntent",j),c.hoverIntent_s=!0,e.over.apply(c,[b])):(h=f,i=g,c.hoverIntent_t=setTimeout(function(){k(b,c)},e.interval),void 0)},l=function(a,b){return b.hoverIntent_t=clearTimeout(b.hoverIntent_t),b.hoverIntent_s=!1,e.out.apply(b,[a])},m=function(b){var c=a.extend({},b),d=this;d.hoverIntent_t&&(d.hoverIntent_t=clearTimeout(d.hoverIntent_t)),"mouseenter"===b.type?(h=c.pageX,i=c.pageY,a(d).on("mousemove.hoverIntent",j),d.hoverIntent_s||(d.hoverIntent_t=setTimeout(function(){k(c,d)},e.interval))):(a(d).off("mousemove.hoverIntent",j),d.hoverIntent_s&&(d.hoverIntent_t=setTimeout(function(){l(c,d)},e.timeout)))};return this.on({"mouseenter.hoverIntent":m,"mouseleave.hoverIntent":m},e.selector)}}(jQuery);
        /* jshint ignore:end */
    }
    jQuery(document).ready(function($){
        var adminbar = $('#wpadminbar'), refresh, touchOpen, touchClose, disableHoverIntent = false;

        refresh = function(i, el){ // force the browser to refresh the tabbing index
            var node = $(el), tab = node.attr('tabindex');
            if ( tab )
                node.attr('tabindex', '0').attr('tabindex', tab);
        };

        touchOpen = function(unbind) {
            adminbar.find('li.menupop').on('click.wp-mobile-hover', function(e) {
                var el = $(this);

                if ( el.parent().is('#wp-admin-bar-root-default') && !el.hasClass('hover') ) {
                    e.preventDefault();
                    adminbar.find('li.menupop.hover').removeClass('hover');
                    el.addClass('hover');
                } else if ( !el.hasClass('hover') ) {
                    e.stopPropagation();
                    e.preventDefault();
                    el.addClass('hover');
                } else if ( ! $( e.target ).closest( 'div' ).hasClass( 'ab-sub-wrapper' ) ) {
                    // We're dealing with an already-touch-opened menu genericon (we know el.hasClass('hover')),
                    // so close it on a second tap and prevent propag and defaults. See #29906
                    e.stopPropagation();
                    e.preventDefault();
                    el.removeClass('hover');
                }

                if ( unbind ) {
                    $('li.menupop').off('click.wp-mobile-hover');
                    disableHoverIntent = false;
                }
            });
        };

        touchClose = function() {
            var mobileEvent = /Mobile\/.+Safari/.test(navigator.userAgent) ? 'touchstart' : 'click';
            // close any open drop-downs when the click/touch is not on the toolbar
            $(document.body).on( mobileEvent+'.wp-mobile-hover', function(e) {
                if ( !$(e.target).closest('#wpadminbar').length )
                    adminbar.find('li.menupop.hover').removeClass('hover');
            });
        };

        adminbar.removeClass('nojq').removeClass('nojs');

        if ( 'ontouchstart' in window ) {
            adminbar.on('touchstart', function(){
                touchOpen(true);
                disableHoverIntent = true;
            });
            touchClose();
        } else if ( /IEMobile\/[1-9]/.test(navigator.userAgent) ) {
            touchOpen();
            touchClose();
        }

        adminbar.find('li.menupop').hoverIntent({
            over: function() {
                if ( disableHoverIntent )
                    return;

                $(this).addClass('hover');
            },
            out: function() {
                if ( disableHoverIntent )
                    return;

                $(this).removeClass('hover');
            },
            timeout: 180,
            sensitivity: 7,
            interval: 100
        });

        if ( window.location.hash )
            window.scrollBy( 0, -32 );

        $('#wp-admin-bar-get-shortlink').click(function(e){
            e.preventDefault();
            $(this).addClass('selected').children('.shortlink-input').blur(function(){
                $(this).parents('#wp-admin-bar-get-shortlink').removeClass('selected');
            }).focus().select();
        });

        $('#wpadminbar li.menupop > .ab-item').bind('keydown.adminbar', function(e){
            if ( e.which != 13 )
                return;

            var target = $(e.target),
                wrap = target.closest('.ab-sub-wrapper'),
                parentHasHover = target.parent().hasClass('hover');

            e.stopPropagation();
            e.preventDefault();

            if ( !wrap.length )
                wrap = $('#wpadminbar .quicklinks');

            wrap.find('.menupop').removeClass('hover');

            if ( ! parentHasHover ) {
                target.parent().toggleClass('hover');
            }

            target.siblings('.ab-sub-wrapper').find('.ab-item').each(refresh);
        }).each(refresh);

        $('#wpadminbar .ab-item').bind('keydown.adminbar', function(e){
            if ( e.which != 27 )
                return;

            var target = $(e.target);

            e.stopPropagation();
            e.preventDefault();

            target.closest('.hover').removeClass('hover').children('.ab-item').focus();
            target.siblings('.ab-sub-wrapper').find('.ab-item').each(refresh);
        });

        adminbar.click( function(e) {
            if ( e.target.id != 'wpadminbar' && e.target.id != 'wp-admin-bar-top-secondary' ) {
                return;
            }

            adminbar.find( 'li.menupop.hover' ).removeClass( 'hover' );
            $( 'html, body' ).animate( { scrollTop: 0 }, 'fast' );
            e.preventDefault();
        });

        // fix focus bug in WebKit
        $('.screen-reader-shortcut').keydown( function(e) {
            var id, ua;

            if ( 13 != e.which )
                return;

            id = $( this ).attr( 'href' );

            ua = navigator.userAgent.toLowerCase();

            if ( ua.indexOf('applewebkit') != -1 && id && id.charAt(0) == '#' ) {
                setTimeout(function () {
                    $(id).focus();
                }, 100);
            }
        });

        $( '#adminbar-search' ).on({
            focus: function() {
                $( '#adminbarsearch' ).addClass( 'adminbar-focused' );
            }, blur: function() {
                $( '#adminbarsearch' ).removeClass( 'adminbar-focused' );
            }
        } );

        // Empty sessionStorage on logging out
        if ( 'sessionStorage' in window ) {
            $('#wp-admin-bar-logout a').click( function() {
                try {
                    for ( var key in sessionStorage ) {
                        if ( key.indexOf('wp-autosave-') != -1 )
                            sessionStorage.removeItem(key);
                    }
                } catch(e) {}
            });
        }

        if ( navigator.userAgent && document.body.className.indexOf( 'no-font-face' ) === -1 &&
            /Android (1.0|1.1|1.5|1.6|2.0|2.1)|Nokia|Opera Mini|w(eb)?OSBrowser|webOS|UCWEB|Windows Phone OS 7|XBLWP7|ZuneWP7|MSIE 7/.test( navigator.userAgent ) ) {

            document.body.className += ' no-font-face';
        }
    });
} else {
    (function(d, w) {
        var addEvent = function( obj, type, fn ) {
                if ( obj.addEventListener )
                    obj.addEventListener(type, fn, false);
                else if ( obj.attachEvent )
                    obj.attachEvent('on' + type, function() { return fn.call(obj, window.event);});
            },

            aB, hc = new RegExp('\\bhover\\b', 'g'), q = [],
            rselected = new RegExp('\\bselected\\b', 'g'),

            /**
             * Get the timeout ID of the given element
             */
            getTOID = function(el) {
                var i = q.length;
                while ( i-- ) {
                    if ( q[i] && el == q[i][1] )
                        return q[i][0];
                }
                return false;
            },

            addHoverClass = function(t) {
                var i, id, inA, hovering, ul, li,
                    ancestors = [],
                    ancestorLength = 0;

                while ( t && t != aB && t != d ) {
                    if ( 'LI' == t.nodeName.toUpperCase() ) {
                        ancestors[ ancestors.length ] = t;
                        id = getTOID(t);
                        if ( id )
                            clearTimeout( id );
                        t.className = t.className ? ( t.className.replace(hc, '') + ' hover' ) : 'hover';
                        hovering = t;
                    }
                    t = t.parentNode;
                }

                // Remove any selected classes.
                if ( hovering && hovering.parentNode ) {
                    ul = hovering.parentNode;
                    if ( ul && 'UL' == ul.nodeName.toUpperCase() ) {
                        i = ul.childNodes.length;
                        while ( i-- ) {
                            li = ul.childNodes[i];
                            if ( li != hovering )
                                li.className = li.className ? li.className.replace( rselected, '' ) : '';
                        }
                    }
                }

                /* remove the hover class for any objects not in the immediate element's ancestry */
                i = q.length;
                while ( i-- ) {
                    inA = false;
                    ancestorLength = ancestors.length;
                    while( ancestorLength-- ) {
                        if ( ancestors[ ancestorLength ] == q[i][1] )
                            inA = true;
                    }

                    if ( ! inA )
                        q[i][1].className = q[i][1].className ? q[i][1].className.replace(hc, '') : '';
                }
            },

            removeHoverClass = function(t) {
                while ( t && t != aB && t != d ) {
                    if ( 'LI' == t.nodeName.toUpperCase() ) {
                        (function(t) {
                            var to = setTimeout(function() {
                                t.className = t.className ? t.className.replace(hc, '') : '';
                            }, 500);
                            q[q.length] = [to, t];
                        })(t);
                    }
                    t = t.parentNode;
                }
            },

            clickShortlink = function(e) {
                var i, l, node,
                    t = e.target || e.srcElement;

                // Make t the shortlink menu item, or return.
                while ( true ) {
                    // Check if we've gone past the shortlink node,
                    // or if the user is clicking on the input.
                    if ( ! t || t == d || t == aB )
                        return;
                    // Check if we've found the shortlink node.
                    if ( t.id && t.id == 'wp-admin-bar-get-shortlink' )
                        break;
                    t = t.parentNode;
                }

                // IE doesn't support preventDefault, and does support returnValue
                if ( e.preventDefault )
                    e.preventDefault();
                e.returnValue = false;

                if ( -1 == t.className.indexOf('selected') )
                    t.className += ' selected';

                for ( i = 0, l = t.childNodes.length; i < l; i++ ) {
                    node = t.childNodes[i];
                    if ( node.className && -1 != node.className.indexOf('shortlink-input') ) {
                        node.focus();
                        node.select();
                        node.onblur = function() {
                            t.className = t.className ? t.className.replace( rselected, '' ) : '';
                        };
                        break;
                    }
                }
                return false;
            },

            scrollToTop = function(t) {
                var distance, speed, step, steps, timer, speed_step;

                // Ensure that the #wpadminbar was the target of the click.
                if ( t.id != 'wpadminbar' && t.id != 'wp-admin-bar-top-secondary' )
                    return;

                distance    = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

                if ( distance < 1 )
                    return;

                speed_step = distance > 800 ? 130 : 100;
                speed     = Math.min( 12, Math.round( distance / speed_step ) );
                step      = distance > 800 ? Math.round( distance / 30  ) : Math.round( distance / 20  );
                steps     = [];
                timer     = 0;

                // Animate scrolling to the top of the page by generating steps to
                // the top of the page and shifting to each step at a set interval.
                while ( distance ) {
                    distance -= step;
                    if ( distance < 0 )
                        distance = 0;
                    steps.push( distance );

                    setTimeout( function() {
                        window.scrollTo( 0, steps.shift() );
                    }, timer * speed );

                    timer++;
                }
            };

        addEvent(w, 'load', function() {
            aB = d.getElementById('wpadminbar');

            if ( d.body && aB ) {
                d.body.appendChild( aB );

                if ( aB.className )
                    aB.className = aB.className.replace(/nojs/, '');

                addEvent(aB, 'mouseover', function(e) {
                    addHoverClass( e.target || e.srcElement );
                });

                addEvent(aB, 'mouseout', function(e) {
                    removeHoverClass( e.target || e.srcElement );
                });

                addEvent(aB, 'click', clickShortlink );

                addEvent(aB, 'click', function(e) {
                    scrollToTop( e.target || e.srcElement );
                });

                addEvent( document.getElementById('wp-admin-bar-logout'), 'click', function() {
                    if ( 'sessionStorage' in window ) {
                        try {
                            for ( var key in sessionStorage ) {
                                if ( key.indexOf('wp-autosave-') != -1 )
                                    sessionStorage.removeItem(key);
                            }
                        } catch(e) {}
                    }
                });
            }

            if ( w.location.hash )
                w.scrollBy(0,-32);

            if ( navigator.userAgent && document.body.className.indexOf( 'no-font-face' ) === -1 &&
                /Android (1.0|1.1|1.5|1.6|2.0|2.1)|Nokia|Opera Mini|w(eb)?OSBrowser|webOS|UCWEB|Windows Phone OS 7|XBLWP7|ZuneWP7|MSIE 7/.test( navigator.userAgent ) ) {

                document.body.className += ' no-font-face';
            }
        });
    })(document, window);

}

//     Underscore.js 1.8.3
//     http://underscorejs.org
//     (c) 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
//     Underscore may be freely distributed under the MIT license.
(function(){function n(n){function t(t,r,e,u,i,o){for(;i>=0&&o>i;i+=n){var a=u?u[i]:i;e=r(e,t[a],a,t)}return e}return function(r,e,u,i){e=b(e,i,4);var o=!k(r)&&m.keys(r),a=(o||r).length,c=n>0?0:a-1;return arguments.length<3&&(u=r[o?o[c]:c],c+=n),t(r,e,u,o,c,a)}}function t(n){return function(t,r,e){r=x(r,e);for(var u=O(t),i=n>0?0:u-1;i>=0&&u>i;i+=n)if(r(t[i],i,t))return i;return-1}}function r(n,t,r){return function(e,u,i){var o=0,a=O(e);if("number"==typeof i)n>0?o=i>=0?i:Math.max(i+a,o):a=i>=0?Math.min(i+1,a):i+a+1;else if(r&&i&&a)return i=r(e,u),e[i]===u?i:-1;if(u!==u)return i=t(l.call(e,o,a),m.isNaN),i>=0?i+o:-1;for(i=n>0?o:a-1;i>=0&&a>i;i+=n)if(e[i]===u)return i;return-1}}function e(n,t){var r=I.length,e=n.constructor,u=m.isFunction(e)&&e.prototype||a,i="constructor";for(m.has(n,i)&&!m.contains(t,i)&&t.push(i);r--;)i=I[r],i in n&&n[i]!==u[i]&&!m.contains(t,i)&&t.push(i)}var u=this,i=u._,o=Array.prototype,a=Object.prototype,c=Function.prototype,f=o.push,l=o.slice,s=a.toString,p=a.hasOwnProperty,h=Array.isArray,v=Object.keys,g=c.bind,y=Object.create,d=function(){},m=function(n){return n instanceof m?n:this instanceof m?void(this._wrapped=n):new m(n)};"undefined"!=typeof exports?("undefined"!=typeof module&&module.exports&&(exports=module.exports=m),exports._=m):u._=m,m.VERSION="1.8.3";var b=function(n,t,r){if(t===void 0)return n;switch(null==r?3:r){case 1:return function(r){return n.call(t,r)};case 2:return function(r,e){return n.call(t,r,e)};case 3:return function(r,e,u){return n.call(t,r,e,u)};case 4:return function(r,e,u,i){return n.call(t,r,e,u,i)}}return function(){return n.apply(t,arguments)}},x=function(n,t,r){return null==n?m.identity:m.isFunction(n)?b(n,t,r):m.isObject(n)?m.matcher(n):m.property(n)};m.iteratee=function(n,t){return x(n,t,1/0)};var _=function(n,t){return function(r){var e=arguments.length;if(2>e||null==r)return r;for(var u=1;e>u;u++)for(var i=arguments[u],o=n(i),a=o.length,c=0;a>c;c++){var f=o[c];t&&r[f]!==void 0||(r[f]=i[f])}return r}},j=function(n){if(!m.isObject(n))return{};if(y)return y(n);d.prototype=n;var t=new d;return d.prototype=null,t},w=function(n){return function(t){return null==t?void 0:t[n]}},A=Math.pow(2,53)-1,O=w("length"),k=function(n){var t=O(n);return"number"==typeof t&&t>=0&&A>=t};m.each=m.forEach=function(n,t,r){t=b(t,r);var e,u;if(k(n))for(e=0,u=n.length;u>e;e++)t(n[e],e,n);else{var i=m.keys(n);for(e=0,u=i.length;u>e;e++)t(n[i[e]],i[e],n)}return n},m.map=m.collect=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=Array(u),o=0;u>o;o++){var a=e?e[o]:o;i[o]=t(n[a],a,n)}return i},m.reduce=m.foldl=m.inject=n(1),m.reduceRight=m.foldr=n(-1),m.find=m.detect=function(n,t,r){var e;return e=k(n)?m.findIndex(n,t,r):m.findKey(n,t,r),e!==void 0&&e!==-1?n[e]:void 0},m.filter=m.select=function(n,t,r){var e=[];return t=x(t,r),m.each(n,function(n,r,u){t(n,r,u)&&e.push(n)}),e},m.reject=function(n,t,r){return m.filter(n,m.negate(x(t)),r)},m.every=m.all=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=0;u>i;i++){var o=e?e[i]:i;if(!t(n[o],o,n))return!1}return!0},m.some=m.any=function(n,t,r){t=x(t,r);for(var e=!k(n)&&m.keys(n),u=(e||n).length,i=0;u>i;i++){var o=e?e[i]:i;if(t(n[o],o,n))return!0}return!1},m.contains=m.includes=m.include=function(n,t,r,e){return k(n)||(n=m.values(n)),("number"!=typeof r||e)&&(r=0),m.indexOf(n,t,r)>=0},m.invoke=function(n,t){var r=l.call(arguments,2),e=m.isFunction(t);return m.map(n,function(n){var u=e?t:n[t];return null==u?u:u.apply(n,r)})},m.pluck=function(n,t){return m.map(n,m.property(t))},m.where=function(n,t){return m.filter(n,m.matcher(t))},m.findWhere=function(n,t){return m.find(n,m.matcher(t))},m.max=function(n,t,r){var e,u,i=-1/0,o=-1/0;if(null==t&&null!=n){n=k(n)?n:m.values(n);for(var a=0,c=n.length;c>a;a++)e=n[a],e>i&&(i=e)}else t=x(t,r),m.each(n,function(n,r,e){u=t(n,r,e),(u>o||u===-1/0&&i===-1/0)&&(i=n,o=u)});return i},m.min=function(n,t,r){var e,u,i=1/0,o=1/0;if(null==t&&null!=n){n=k(n)?n:m.values(n);for(var a=0,c=n.length;c>a;a++)e=n[a],i>e&&(i=e)}else t=x(t,r),m.each(n,function(n,r,e){u=t(n,r,e),(o>u||1/0===u&&1/0===i)&&(i=n,o=u)});return i},m.shuffle=function(n){for(var t,r=k(n)?n:m.values(n),e=r.length,u=Array(e),i=0;e>i;i++)t=m.random(0,i),t!==i&&(u[i]=u[t]),u[t]=r[i];return u},m.sample=function(n,t,r){return null==t||r?(k(n)||(n=m.values(n)),n[m.random(n.length-1)]):m.shuffle(n).slice(0,Math.max(0,t))},m.sortBy=function(n,t,r){return t=x(t,r),m.pluck(m.map(n,function(n,r,e){return{value:n,index:r,criteria:t(n,r,e)}}).sort(function(n,t){var r=n.criteria,e=t.criteria;if(r!==e){if(r>e||r===void 0)return 1;if(e>r||e===void 0)return-1}return n.index-t.index}),"value")};var F=function(n){return function(t,r,e){var u={};return r=x(r,e),m.each(t,function(e,i){var o=r(e,i,t);n(u,e,o)}),u}};m.groupBy=F(function(n,t,r){m.has(n,r)?n[r].push(t):n[r]=[t]}),m.indexBy=F(function(n,t,r){n[r]=t}),m.countBy=F(function(n,t,r){m.has(n,r)?n[r]++:n[r]=1}),m.toArray=function(n){return n?m.isArray(n)?l.call(n):k(n)?m.map(n,m.identity):m.values(n):[]},m.size=function(n){return null==n?0:k(n)?n.length:m.keys(n).length},m.partition=function(n,t,r){t=x(t,r);var e=[],u=[];return m.each(n,function(n,r,i){(t(n,r,i)?e:u).push(n)}),[e,u]},m.first=m.head=m.take=function(n,t,r){return null==n?void 0:null==t||r?n[0]:m.initial(n,n.length-t)},m.initial=function(n,t,r){return l.call(n,0,Math.max(0,n.length-(null==t||r?1:t)))},m.last=function(n,t,r){return null==n?void 0:null==t||r?n[n.length-1]:m.rest(n,Math.max(0,n.length-t))},m.rest=m.tail=m.drop=function(n,t,r){return l.call(n,null==t||r?1:t)},m.compact=function(n){return m.filter(n,m.identity)};var S=function(n,t,r,e){for(var u=[],i=0,o=e||0,a=O(n);a>o;o++){var c=n[o];if(k(c)&&(m.isArray(c)||m.isArguments(c))){t||(c=S(c,t,r));var f=0,l=c.length;for(u.length+=l;l>f;)u[i++]=c[f++]}else r||(u[i++]=c)}return u};m.flatten=function(n,t){return S(n,t,!1)},m.without=function(n){return m.difference(n,l.call(arguments,1))},m.uniq=m.unique=function(n,t,r,e){m.isBoolean(t)||(e=r,r=t,t=!1),null!=r&&(r=x(r,e));for(var u=[],i=[],o=0,a=O(n);a>o;o++){var c=n[o],f=r?r(c,o,n):c;t?(o&&i===f||u.push(c),i=f):r?m.contains(i,f)||(i.push(f),u.push(c)):m.contains(u,c)||u.push(c)}return u},m.union=function(){return m.uniq(S(arguments,!0,!0))},m.intersection=function(n){for(var t=[],r=arguments.length,e=0,u=O(n);u>e;e++){var i=n[e];if(!m.contains(t,i)){for(var o=1;r>o&&m.contains(arguments[o],i);o++);o===r&&t.push(i)}}return t},m.difference=function(n){var t=S(arguments,!0,!0,1);return m.filter(n,function(n){return!m.contains(t,n)})},m.zip=function(){return m.unzip(arguments)},m.unzip=function(n){for(var t=n&&m.max(n,O).length||0,r=Array(t),e=0;t>e;e++)r[e]=m.pluck(n,e);return r},m.object=function(n,t){for(var r={},e=0,u=O(n);u>e;e++)t?r[n[e]]=t[e]:r[n[e][0]]=n[e][1];return r},m.findIndex=t(1),m.findLastIndex=t(-1),m.sortedIndex=function(n,t,r,e){r=x(r,e,1);for(var u=r(t),i=0,o=O(n);o>i;){var a=Math.floor((i+o)/2);r(n[a])<u?i=a+1:o=a}return i},m.indexOf=r(1,m.findIndex,m.sortedIndex),m.lastIndexOf=r(-1,m.findLastIndex),m.range=function(n,t,r){null==t&&(t=n||0,n=0),r=r||1;for(var e=Math.max(Math.ceil((t-n)/r),0),u=Array(e),i=0;e>i;i++,n+=r)u[i]=n;return u};var E=function(n,t,r,e,u){if(!(e instanceof t))return n.apply(r,u);var i=j(n.prototype),o=n.apply(i,u);return m.isObject(o)?o:i};m.bind=function(n,t){if(g&&n.bind===g)return g.apply(n,l.call(arguments,1));if(!m.isFunction(n))throw new TypeError("Bind must be called on a function");var r=l.call(arguments,2),e=function(){return E(n,e,t,this,r.concat(l.call(arguments)))};return e},m.partial=function(n){var t=l.call(arguments,1),r=function(){for(var e=0,u=t.length,i=Array(u),o=0;u>o;o++)i[o]=t[o]===m?arguments[e++]:t[o];for(;e<arguments.length;)i.push(arguments[e++]);return E(n,r,this,this,i)};return r},m.bindAll=function(n){var t,r,e=arguments.length;if(1>=e)throw new Error("bindAll must be passed function names");for(t=1;e>t;t++)r=arguments[t],n[r]=m.bind(n[r],n);return n},m.memoize=function(n,t){var r=function(e){var u=r.cache,i=""+(t?t.apply(this,arguments):e);return m.has(u,i)||(u[i]=n.apply(this,arguments)),u[i]};return r.cache={},r},m.delay=function(n,t){var r=l.call(arguments,2);return setTimeout(function(){return n.apply(null,r)},t)},m.defer=m.partial(m.delay,m,1),m.throttle=function(n,t,r){var e,u,i,o=null,a=0;r||(r={});var c=function(){a=r.leading===!1?0:m.now(),o=null,i=n.apply(e,u),o||(e=u=null)};return function(){var f=m.now();a||r.leading!==!1||(a=f);var l=t-(f-a);return e=this,u=arguments,0>=l||l>t?(o&&(clearTimeout(o),o=null),a=f,i=n.apply(e,u),o||(e=u=null)):o||r.trailing===!1||(o=setTimeout(c,l)),i}},m.debounce=function(n,t,r){var e,u,i,o,a,c=function(){var f=m.now()-o;t>f&&f>=0?e=setTimeout(c,t-f):(e=null,r||(a=n.apply(i,u),e||(i=u=null)))};return function(){i=this,u=arguments,o=m.now();var f=r&&!e;return e||(e=setTimeout(c,t)),f&&(a=n.apply(i,u),i=u=null),a}},m.wrap=function(n,t){return m.partial(t,n)},m.negate=function(n){return function(){return!n.apply(this,arguments)}},m.compose=function(){var n=arguments,t=n.length-1;return function(){for(var r=t,e=n[t].apply(this,arguments);r--;)e=n[r].call(this,e);return e}},m.after=function(n,t){return function(){return--n<1?t.apply(this,arguments):void 0}},m.before=function(n,t){var r;return function(){return--n>0&&(r=t.apply(this,arguments)),1>=n&&(t=null),r}},m.once=m.partial(m.before,2);var M=!{toString:null}.propertyIsEnumerable("toString"),I=["valueOf","isPrototypeOf","toString","propertyIsEnumerable","hasOwnProperty","toLocaleString"];m.keys=function(n){if(!m.isObject(n))return[];if(v)return v(n);var t=[];for(var r in n)m.has(n,r)&&t.push(r);return M&&e(n,t),t},m.allKeys=function(n){if(!m.isObject(n))return[];var t=[];for(var r in n)t.push(r);return M&&e(n,t),t},m.values=function(n){for(var t=m.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=n[t[u]];return e},m.mapObject=function(n,t,r){t=x(t,r);for(var e,u=m.keys(n),i=u.length,o={},a=0;i>a;a++)e=u[a],o[e]=t(n[e],e,n);return o},m.pairs=function(n){for(var t=m.keys(n),r=t.length,e=Array(r),u=0;r>u;u++)e[u]=[t[u],n[t[u]]];return e},m.invert=function(n){for(var t={},r=m.keys(n),e=0,u=r.length;u>e;e++)t[n[r[e]]]=r[e];return t},m.functions=m.methods=function(n){var t=[];for(var r in n)m.isFunction(n[r])&&t.push(r);return t.sort()},m.extend=_(m.allKeys),m.extendOwn=m.assign=_(m.keys),m.findKey=function(n,t,r){t=x(t,r);for(var e,u=m.keys(n),i=0,o=u.length;o>i;i++)if(e=u[i],t(n[e],e,n))return e},m.pick=function(n,t,r){var e,u,i={},o=n;if(null==o)return i;m.isFunction(t)?(u=m.allKeys(o),e=b(t,r)):(u=S(arguments,!1,!1,1),e=function(n,t,r){return t in r},o=Object(o));for(var a=0,c=u.length;c>a;a++){var f=u[a],l=o[f];e(l,f,o)&&(i[f]=l)}return i},m.omit=function(n,t,r){if(m.isFunction(t))t=m.negate(t);else{var e=m.map(S(arguments,!1,!1,1),String);t=function(n,t){return!m.contains(e,t)}}return m.pick(n,t,r)},m.defaults=_(m.allKeys,!0),m.create=function(n,t){var r=j(n);return t&&m.extendOwn(r,t),r},m.clone=function(n){return m.isObject(n)?m.isArray(n)?n.slice():m.extend({},n):n},m.tap=function(n,t){return t(n),n},m.isMatch=function(n,t){var r=m.keys(t),e=r.length;if(null==n)return!e;for(var u=Object(n),i=0;e>i;i++){var o=r[i];if(t[o]!==u[o]||!(o in u))return!1}return!0};var N=function(n,t,r,e){if(n===t)return 0!==n||1/n===1/t;if(null==n||null==t)return n===t;n instanceof m&&(n=n._wrapped),t instanceof m&&(t=t._wrapped);var u=s.call(n);if(u!==s.call(t))return!1;switch(u){case"[object RegExp]":case"[object String]":return""+n==""+t;case"[object Number]":return+n!==+n?+t!==+t:0===+n?1/+n===1/t:+n===+t;case"[object Date]":case"[object Boolean]":return+n===+t}var i="[object Array]"===u;if(!i){if("object"!=typeof n||"object"!=typeof t)return!1;var o=n.constructor,a=t.constructor;if(o!==a&&!(m.isFunction(o)&&o instanceof o&&m.isFunction(a)&&a instanceof a)&&"constructor"in n&&"constructor"in t)return!1}r=r||[],e=e||[];for(var c=r.length;c--;)if(r[c]===n)return e[c]===t;if(r.push(n),e.push(t),i){if(c=n.length,c!==t.length)return!1;for(;c--;)if(!N(n[c],t[c],r,e))return!1}else{var f,l=m.keys(n);if(c=l.length,m.keys(t).length!==c)return!1;for(;c--;)if(f=l[c],!m.has(t,f)||!N(n[f],t[f],r,e))return!1}return r.pop(),e.pop(),!0};m.isEqual=function(n,t){return N(n,t)},m.isEmpty=function(n){return null==n?!0:k(n)&&(m.isArray(n)||m.isString(n)||m.isArguments(n))?0===n.length:0===m.keys(n).length},m.isElement=function(n){return!(!n||1!==n.nodeType)},m.isArray=h||function(n){return"[object Array]"===s.call(n)},m.isObject=function(n){var t=typeof n;return"function"===t||"object"===t&&!!n},m.each(["Arguments","Function","String","Number","Date","RegExp","Error"],function(n){m["is"+n]=function(t){return s.call(t)==="[object "+n+"]"}}),m.isArguments(arguments)||(m.isArguments=function(n){return m.has(n,"callee")}),"function"!=typeof/./&&"object"!=typeof Int8Array&&(m.isFunction=function(n){return"function"==typeof n||!1}),m.isFinite=function(n){return isFinite(n)&&!isNaN(parseFloat(n))},m.isNaN=function(n){return m.isNumber(n)&&n!==+n},m.isBoolean=function(n){return n===!0||n===!1||"[object Boolean]"===s.call(n)},m.isNull=function(n){return null===n},m.isUndefined=function(n){return n===void 0},m.has=function(n,t){return null!=n&&p.call(n,t)},m.noConflict=function(){return u._=i,this},m.identity=function(n){return n},m.constant=function(n){return function(){return n}},m.noop=function(){},m.property=w,m.propertyOf=function(n){return null==n?function(){}:function(t){return n[t]}},m.matcher=m.matches=function(n){return n=m.extendOwn({},n),function(t){return m.isMatch(t,n)}},m.times=function(n,t,r){var e=Array(Math.max(0,n));t=b(t,r,1);for(var u=0;n>u;u++)e[u]=t(u);return e},m.random=function(n,t){return null==t&&(t=n,n=0),n+Math.floor(Math.random()*(t-n+1))},m.now=Date.now||function(){return(new Date).getTime()};var B={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},T=m.invert(B),R=function(n){var t=function(t){return n[t]},r="(?:"+m.keys(n).join("|")+")",e=RegExp(r),u=RegExp(r,"g");return function(n){return n=null==n?"":""+n,e.test(n)?n.replace(u,t):n}};m.escape=R(B),m.unescape=R(T),m.result=function(n,t,r){var e=null==n?void 0:n[t];return e===void 0&&(e=r),m.isFunction(e)?e.call(n):e};var q=0;m.uniqueId=function(n){var t=++q+"";return n?n+t:t},m.templateSettings={evaluate:/<%([\s\S]+?)%>/g,interpolate:/<%=([\s\S]+?)%>/g,escape:/<%-([\s\S]+?)%>/g};var K=/(.)^/,z={"'":"'","\\":"\\","\r":"r","\n":"n","\u2028":"u2028","\u2029":"u2029"},D=/\\|'|\r|\n|\u2028|\u2029/g,L=function(n){return"\\"+z[n]};m.template=function(n,t,r){!t&&r&&(t=r),t=m.defaults({},t,m.templateSettings);var e=RegExp([(t.escape||K).source,(t.interpolate||K).source,(t.evaluate||K).source].join("|")+"|$","g"),u=0,i="__p+='";n.replace(e,function(t,r,e,o,a){return i+=n.slice(u,a).replace(D,L),u=a+t.length,r?i+="'+\n((__t=("+r+"))==null?'':_.escape(__t))+\n'":e?i+="'+\n((__t=("+e+"))==null?'':__t)+\n'":o&&(i+="';\n"+o+"\n__p+='"),t}),i+="';\n",t.variable||(i="with(obj||{}){\n"+i+"}\n"),i="var __t,__p='',__j=Array.prototype.join,"+"print=function(){__p+=__j.call(arguments,'');};\n"+i+"return __p;\n";try{var o=new Function(t.variable||"obj","_",i)}catch(a){throw a.source=i,a}var c=function(n){return o.call(this,n,m)},f=t.variable||"obj";return c.source="function("+f+"){\n"+i+"}",c},m.chain=function(n){var t=m(n);return t._chain=!0,t};var P=function(n,t){return n._chain?m(t).chain():t};m.mixin=function(n){m.each(m.functions(n),function(t){var r=m[t]=n[t];m.prototype[t]=function(){var n=[this._wrapped];return f.apply(n,arguments),P(this,r.apply(m,n))}})},m.mixin(m),m.each(["pop","push","reverse","shift","sort","splice","unshift"],function(n){var t=o[n];m.prototype[n]=function(){var r=this._wrapped;return t.apply(r,arguments),"shift"!==n&&"splice"!==n||0!==r.length||delete r[0],P(this,r)}}),m.each(["concat","join","slice"],function(n){var t=o[n];m.prototype[n]=function(){return P(this,t.apply(this._wrapped,arguments))}}),m.prototype.value=function(){return this._wrapped},m.prototype.valueOf=m.prototype.toJSON=m.prototype.value,m.prototype.toString=function(){return""+this._wrapped},"function"==typeof define&&define.amd&&define("underscore",[],function(){return m})}).call(this);

window.wp=window.wp||{},function(){wp.shortcode={next:function(a,b,c){var d,e,f=wp.shortcode.regexp(a);if(f.lastIndex=c||0,d=f.exec(b))return"["===d[1]&&"]"===d[7]?wp.shortcode.next(a,b,f.lastIndex):(e={index:d.index,content:d[0],shortcode:wp.shortcode.fromMatch(d)},d[1]&&(e.content=e.content.slice(1),e.index++),d[7]&&(e.content=e.content.slice(0,-1)),e)},replace:function(a,b,c){return b.replace(wp.shortcode.regexp(a),function(a,b,d,e,f,g,h,i){if("["===b&&"]"===i)return a;var j=c(wp.shortcode.fromMatch(arguments));return j?b+j+i:a})},string:function(a){return new wp.shortcode(a).string()},regexp:_.memoize(function(a){return new RegExp("\\[(\\[?)("+a+")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)","g")}),attrs:_.memoize(function(a){var b,c,d={},e=[];for(b=/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*'([^']*)'(?:\s|$)|([\w-]+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|'([^']*)'(?:\s|$)|(\S+)(?:\s|$)/g,a=a.replace(/[\u00a0\u200b]/g," ");c=b.exec(a);)c[1]?d[c[1].toLowerCase()]=c[2]:c[3]?d[c[3].toLowerCase()]=c[4]:c[5]?d[c[5].toLowerCase()]=c[6]:c[7]?e.push(c[7]):c[8]?e.push(c[8]):c[9]&&e.push(c[9]);return{named:d,numeric:e}}),fromMatch:function(a){var b;return b=a[4]?"self-closing":a[6]?"closed":"single",new wp.shortcode({tag:a[2],attrs:a[3],type:b,content:a[5]})}},wp.shortcode=_.extend(function(a){_.extend(this,_.pick(a||{},"tag","attrs","type","content"));var b=this.attrs;this.attrs={named:{},numeric:[]},b&&(_.isString(b)?this.attrs=wp.shortcode.attrs(b):_.isEqual(_.keys(b),["named","numeric"])?this.attrs=b:_.each(a.attrs,function(a,b){this.set(b,a)},this))},wp.shortcode),_.extend(wp.shortcode.prototype,{get:function(a){return this.attrs[_.isNumber(a)?"numeric":"named"][a]},set:function(a,b){return this.attrs[_.isNumber(a)?"numeric":"named"][a]=b,this},string:function(){var a="["+this.tag;return _.each(this.attrs.numeric,function(b){a+=/\s/.test(b)?' "'+b+'"':" "+b}),_.each(this.attrs.named,function(b,c){a+=" "+c+'="'+b+'"'}),"single"===this.type?a+"]":"self-closing"===this.type?a+" /]":(a+="]",this.content&&(a+=this.content),a+"[/"+this.tag+"]")}})}(),function(){wp.html=_.extend(wp.html||{},{attrs:function(a){var b,c;return"/"===a[a.length-1]&&(a=a.slice(0,-1)),b=wp.shortcode.attrs(a),c=b.named,_.each(b.numeric,function(a){/\s/.test(a)||(c[a]="")}),c},string:function(a){var b="<"+a.tag,c=a.content||"";return _.each(a.attrs,function(a,c){b+=" "+c,_.isBoolean(a)&&(a=a?"true":"false"),b+='="'+a+'"'}),a.single?b+" />":(b+=">",b+=_.isObject(c)?wp.html.string(c):c,b+"</"+a.tag+">")}})}();
(function(t){var e=typeof self=="object"&&self.self===self&&self||typeof global=="object"&&global.global===global&&global;if(typeof define==="function"&&define.amd){define(["underscore","jquery","exports"],function(i,r,n){e.Backbone=t(e,n,i,r)})}else if(typeof exports!=="undefined"){var i=require("underscore"),r;try{r=require("jquery")}catch(n){}t(e,exports,i,r)}else{e.Backbone=t(e,{},e._,e.jQuery||e.Zepto||e.ender||e.$)}})(function(t,e,i,r){var n=t.Backbone;var s=Array.prototype.slice;e.VERSION="1.3.3";e.$=r;e.noConflict=function(){t.Backbone=n;return this};e.emulateHTTP=false;e.emulateJSON=false;var a=function(t,e,r){switch(t){case 1:return function(){return i[e](this[r])};case 2:return function(t){return i[e](this[r],t)};case 3:return function(t,n){return i[e](this[r],o(t,this),n)};case 4:return function(t,n,s){return i[e](this[r],o(t,this),n,s)};default:return function(){var t=s.call(arguments);t.unshift(this[r]);return i[e].apply(i,t)}}};var h=function(t,e,r){i.each(e,function(e,n){if(i[n])t.prototype[n]=a(e,n,r)})};var o=function(t,e){if(i.isFunction(t))return t;if(i.isObject(t)&&!e._isModel(t))return l(t);if(i.isString(t))return function(e){return e.get(t)};return t};var l=function(t){var e=i.matches(t);return function(t){return e(t.attributes)}};var u=e.Events={};var c=/\s+/;var f=function(t,e,r,n,s){var a=0,h;if(r&&typeof r==="object"){if(n!==void 0&&"context"in s&&s.context===void 0)s.context=n;for(h=i.keys(r);a<h.length;a++){e=f(t,e,h[a],r[h[a]],s)}}else if(r&&c.test(r)){for(h=r.split(c);a<h.length;a++){e=t(e,h[a],n,s)}}else{e=t(e,r,n,s)}return e};u.on=function(t,e,i){return d(this,t,e,i)};var d=function(t,e,i,r,n){t._events=f(v,t._events||{},e,i,{context:r,ctx:t,listening:n});if(n){var s=t._listeners||(t._listeners={});s[n.id]=n}return t};u.listenTo=function(t,e,r){if(!t)return this;var n=t._listenId||(t._listenId=i.uniqueId("l"));var s=this._listeningTo||(this._listeningTo={});var a=s[n];if(!a){var h=this._listenId||(this._listenId=i.uniqueId("l"));a=s[n]={obj:t,objId:n,id:h,listeningTo:s,count:0}}d(t,e,r,this,a);return this};var v=function(t,e,i,r){if(i){var n=t[e]||(t[e]=[]);var s=r.context,a=r.ctx,h=r.listening;if(h)h.count++;n.push({callback:i,context:s,ctx:s||a,listening:h})}return t};u.off=function(t,e,i){if(!this._events)return this;this._events=f(g,this._events,t,e,{context:i,listeners:this._listeners});return this};u.stopListening=function(t,e,r){var n=this._listeningTo;if(!n)return this;var s=t?[t._listenId]:i.keys(n);for(var a=0;a<s.length;a++){var h=n[s[a]];if(!h)break;h.obj.off(e,r,this)}return this};var g=function(t,e,r,n){if(!t)return;var s=0,a;var h=n.context,o=n.listeners;if(!e&&!r&&!h){var l=i.keys(o);for(;s<l.length;s++){a=o[l[s]];delete o[a.id];delete a.listeningTo[a.objId]}return}var u=e?[e]:i.keys(t);for(;s<u.length;s++){e=u[s];var c=t[e];if(!c)break;var f=[];for(var d=0;d<c.length;d++){var v=c[d];if(r&&r!==v.callback&&r!==v.callback._callback||h&&h!==v.context){f.push(v)}else{a=v.listening;if(a&&--a.count===0){delete o[a.id];delete a.listeningTo[a.objId]}}}if(f.length){t[e]=f}else{delete t[e]}}return t};u.once=function(t,e,r){var n=f(p,{},t,e,i.bind(this.off,this));if(typeof t==="string"&&r==null)e=void 0;return this.on(n,e,r)};u.listenToOnce=function(t,e,r){var n=f(p,{},e,r,i.bind(this.stopListening,this,t));return this.listenTo(t,n)};var p=function(t,e,r,n){if(r){var s=t[e]=i.once(function(){n(e,s);r.apply(this,arguments)});s._callback=r}return t};u.trigger=function(t){if(!this._events)return this;var e=Math.max(0,arguments.length-1);var i=Array(e);for(var r=0;r<e;r++)i[r]=arguments[r+1];f(m,this._events,t,void 0,i);return this};var m=function(t,e,i,r){if(t){var n=t[e];var s=t.all;if(n&&s)s=s.slice();if(n)_(n,r);if(s)_(s,[e].concat(r))}return t};var _=function(t,e){var i,r=-1,n=t.length,s=e[0],a=e[1],h=e[2];switch(e.length){case 0:while(++r<n)(i=t[r]).callback.call(i.ctx);return;case 1:while(++r<n)(i=t[r]).callback.call(i.ctx,s);return;case 2:while(++r<n)(i=t[r]).callback.call(i.ctx,s,a);return;case 3:while(++r<n)(i=t[r]).callback.call(i.ctx,s,a,h);return;default:while(++r<n)(i=t[r]).callback.apply(i.ctx,e);return}};u.bind=u.on;u.unbind=u.off;i.extend(e,u);var y=e.Model=function(t,e){var r=t||{};e||(e={});this.cid=i.uniqueId(this.cidPrefix);this.attributes={};if(e.collection)this.collection=e.collection;if(e.parse)r=this.parse(r,e)||{};var n=i.result(this,"defaults");r=i.defaults(i.extend({},n,r),n);this.set(r,e);this.changed={};this.initialize.apply(this,arguments)};i.extend(y.prototype,u,{changed:null,validationError:null,idAttribute:"id",cidPrefix:"c",initialize:function(){},toJSON:function(t){return i.clone(this.attributes)},sync:function(){return e.sync.apply(this,arguments)},get:function(t){return this.attributes[t]},escape:function(t){return i.escape(this.get(t))},has:function(t){return this.get(t)!=null},matches:function(t){return!!i.iteratee(t,this)(this.attributes)},set:function(t,e,r){if(t==null)return this;var n;if(typeof t==="object"){n=t;r=e}else{(n={})[t]=e}r||(r={});if(!this._validate(n,r))return false;var s=r.unset;var a=r.silent;var h=[];var o=this._changing;this._changing=true;if(!o){this._previousAttributes=i.clone(this.attributes);this.changed={}}var l=this.attributes;var u=this.changed;var c=this._previousAttributes;for(var f in n){e=n[f];if(!i.isEqual(l[f],e))h.push(f);if(!i.isEqual(c[f],e)){u[f]=e}else{delete u[f]}s?delete l[f]:l[f]=e}if(this.idAttribute in n)this.id=this.get(this.idAttribute);if(!a){if(h.length)this._pending=r;for(var d=0;d<h.length;d++){this.trigger("change:"+h[d],this,l[h[d]],r)}}if(o)return this;if(!a){while(this._pending){r=this._pending;this._pending=false;this.trigger("change",this,r)}}this._pending=false;this._changing=false;return this},unset:function(t,e){return this.set(t,void 0,i.extend({},e,{unset:true}))},clear:function(t){var e={};for(var r in this.attributes)e[r]=void 0;return this.set(e,i.extend({},t,{unset:true}))},hasChanged:function(t){if(t==null)return!i.isEmpty(this.changed);return i.has(this.changed,t)},changedAttributes:function(t){if(!t)return this.hasChanged()?i.clone(this.changed):false;var e=this._changing?this._previousAttributes:this.attributes;var r={};for(var n in t){var s=t[n];if(i.isEqual(e[n],s))continue;r[n]=s}return i.size(r)?r:false},previous:function(t){if(t==null||!this._previousAttributes)return null;return this._previousAttributes[t]},previousAttributes:function(){return i.clone(this._previousAttributes)},fetch:function(t){t=i.extend({parse:true},t);var e=this;var r=t.success;t.success=function(i){var n=t.parse?e.parse(i,t):i;if(!e.set(n,t))return false;if(r)r.call(t.context,e,i,t);e.trigger("sync",e,i,t)};B(this,t);return this.sync("read",this,t)},save:function(t,e,r){var n;if(t==null||typeof t==="object"){n=t;r=e}else{(n={})[t]=e}r=i.extend({validate:true,parse:true},r);var s=r.wait;if(n&&!s){if(!this.set(n,r))return false}else if(!this._validate(n,r)){return false}var a=this;var h=r.success;var o=this.attributes;r.success=function(t){a.attributes=o;var e=r.parse?a.parse(t,r):t;if(s)e=i.extend({},n,e);if(e&&!a.set(e,r))return false;if(h)h.call(r.context,a,t,r);a.trigger("sync",a,t,r)};B(this,r);if(n&&s)this.attributes=i.extend({},o,n);var l=this.isNew()?"create":r.patch?"patch":"update";if(l==="patch"&&!r.attrs)r.attrs=n;var u=this.sync(l,this,r);this.attributes=o;return u},destroy:function(t){t=t?i.clone(t):{};var e=this;var r=t.success;var n=t.wait;var s=function(){e.stopListening();e.trigger("destroy",e,e.collection,t)};t.success=function(i){if(n)s();if(r)r.call(t.context,e,i,t);if(!e.isNew())e.trigger("sync",e,i,t)};var a=false;if(this.isNew()){i.defer(t.success)}else{B(this,t);a=this.sync("delete",this,t)}if(!n)s();return a},url:function(){var t=i.result(this,"urlRoot")||i.result(this.collection,"url")||F();if(this.isNew())return t;var e=this.get(this.idAttribute);return t.replace(/[^\/]$/,"$&/")+encodeURIComponent(e)},parse:function(t,e){return t},clone:function(){return new this.constructor(this.attributes)},isNew:function(){return!this.has(this.idAttribute)},isValid:function(t){return this._validate({},i.extend({},t,{validate:true}))},_validate:function(t,e){if(!e.validate||!this.validate)return true;t=i.extend({},this.attributes,t);var r=this.validationError=this.validate(t,e)||null;if(!r)return true;this.trigger("invalid",this,r,i.extend(e,{validationError:r}));return false}});var b={keys:1,values:1,pairs:1,invert:1,pick:0,omit:0,chain:1,isEmpty:1};h(y,b,"attributes");var x=e.Collection=function(t,e){e||(e={});if(e.model)this.model=e.model;if(e.comparator!==void 0)this.comparator=e.comparator;this._reset();this.initialize.apply(this,arguments);if(t)this.reset(t,i.extend({silent:true},e))};var w={add:true,remove:true,merge:true};var E={add:true,remove:false};var I=function(t,e,i){i=Math.min(Math.max(i,0),t.length);var r=Array(t.length-i);var n=e.length;var s;for(s=0;s<r.length;s++)r[s]=t[s+i];for(s=0;s<n;s++)t[s+i]=e[s];for(s=0;s<r.length;s++)t[s+n+i]=r[s]};i.extend(x.prototype,u,{model:y,initialize:function(){},toJSON:function(t){return this.map(function(e){return e.toJSON(t)})},sync:function(){return e.sync.apply(this,arguments)},add:function(t,e){return this.set(t,i.extend({merge:false},e,E))},remove:function(t,e){e=i.extend({},e);var r=!i.isArray(t);t=r?[t]:t.slice();var n=this._removeModels(t,e);if(!e.silent&&n.length){e.changes={added:[],merged:[],removed:n};this.trigger("update",this,e)}return r?n[0]:n},set:function(t,e){if(t==null)return;e=i.extend({},w,e);if(e.parse&&!this._isModel(t)){t=this.parse(t,e)||[]}var r=!i.isArray(t);t=r?[t]:t.slice();var n=e.at;if(n!=null)n=+n;if(n>this.length)n=this.length;if(n<0)n+=this.length+1;var s=[];var a=[];var h=[];var o=[];var l={};var u=e.add;var c=e.merge;var f=e.remove;var d=false;var v=this.comparator&&n==null&&e.sort!==false;var g=i.isString(this.comparator)?this.comparator:null;var p,m;for(m=0;m<t.length;m++){p=t[m];var _=this.get(p);if(_){if(c&&p!==_){var y=this._isModel(p)?p.attributes:p;if(e.parse)y=_.parse(y,e);_.set(y,e);h.push(_);if(v&&!d)d=_.hasChanged(g)}if(!l[_.cid]){l[_.cid]=true;s.push(_)}t[m]=_}else if(u){p=t[m]=this._prepareModel(p,e);if(p){a.push(p);this._addReference(p,e);l[p.cid]=true;s.push(p)}}}if(f){for(m=0;m<this.length;m++){p=this.models[m];if(!l[p.cid])o.push(p)}if(o.length)this._removeModels(o,e)}var b=false;var x=!v&&u&&f;if(s.length&&x){b=this.length!==s.length||i.some(this.models,function(t,e){return t!==s[e]});this.models.length=0;I(this.models,s,0);this.length=this.models.length}else if(a.length){if(v)d=true;I(this.models,a,n==null?this.length:n);this.length=this.models.length}if(d)this.sort({silent:true});if(!e.silent){for(m=0;m<a.length;m++){if(n!=null)e.index=n+m;p=a[m];p.trigger("add",p,this,e)}if(d||b)this.trigger("sort",this,e);if(a.length||o.length||h.length){e.changes={added:a,removed:o,merged:h};this.trigger("update",this,e)}}return r?t[0]:t},reset:function(t,e){e=e?i.clone(e):{};for(var r=0;r<this.models.length;r++){this._removeReference(this.models[r],e)}e.previousModels=this.models;this._reset();t=this.add(t,i.extend({silent:true},e));if(!e.silent)this.trigger("reset",this,e);return t},push:function(t,e){return this.add(t,i.extend({at:this.length},e))},pop:function(t){var e=this.at(this.length-1);return this.remove(e,t)},unshift:function(t,e){return this.add(t,i.extend({at:0},e))},shift:function(t){var e=this.at(0);return this.remove(e,t)},slice:function(){return s.apply(this.models,arguments)},get:function(t){if(t==null)return void 0;return this._byId[t]||this._byId[this.modelId(t.attributes||t)]||t.cid&&this._byId[t.cid]},has:function(t){return this.get(t)!=null},at:function(t){if(t<0)t+=this.length;return this.models[t]},where:function(t,e){return this[e?"find":"filter"](t)},findWhere:function(t){return this.where(t,true)},sort:function(t){var e=this.comparator;if(!e)throw new Error("Cannot sort a set without a comparator");t||(t={});var r=e.length;if(i.isFunction(e))e=i.bind(e,this);if(r===1||i.isString(e)){this.models=this.sortBy(e)}else{this.models.sort(e)}if(!t.silent)this.trigger("sort",this,t);return this},pluck:function(t){return this.map(t+"")},fetch:function(t){t=i.extend({parse:true},t);var e=t.success;var r=this;t.success=function(i){var n=t.reset?"reset":"set";r[n](i,t);if(e)e.call(t.context,r,i,t);r.trigger("sync",r,i,t)};B(this,t);return this.sync("read",this,t)},create:function(t,e){e=e?i.clone(e):{};var r=e.wait;t=this._prepareModel(t,e);if(!t)return false;if(!r)this.add(t,e);var n=this;var s=e.success;e.success=function(t,e,i){if(r)n.add(t,i);if(s)s.call(i.context,t,e,i)};t.save(null,e);return t},parse:function(t,e){return t},clone:function(){return new this.constructor(this.models,{model:this.model,comparator:this.comparator})},modelId:function(t){return t[this.model.prototype.idAttribute||"id"]},_reset:function(){this.length=0;this.models=[];this._byId={}},_prepareModel:function(t,e){if(this._isModel(t)){if(!t.collection)t.collection=this;return t}e=e?i.clone(e):{};e.collection=this;var r=new this.model(t,e);if(!r.validationError)return r;this.trigger("invalid",this,r.validationError,e);return false},_removeModels:function(t,e){var i=[];for(var r=0;r<t.length;r++){var n=this.get(t[r]);if(!n)continue;var s=this.indexOf(n);this.models.splice(s,1);this.length--;delete this._byId[n.cid];var a=this.modelId(n.attributes);if(a!=null)delete this._byId[a];if(!e.silent){e.index=s;n.trigger("remove",n,this,e)}i.push(n);this._removeReference(n,e)}return i},_isModel:function(t){return t instanceof y},_addReference:function(t,e){this._byId[t.cid]=t;var i=this.modelId(t.attributes);if(i!=null)this._byId[i]=t;t.on("all",this._onModelEvent,this)},_removeReference:function(t,e){delete this._byId[t.cid];var i=this.modelId(t.attributes);if(i!=null)delete this._byId[i];if(this===t.collection)delete t.collection;t.off("all",this._onModelEvent,this)},_onModelEvent:function(t,e,i,r){if(e){if((t==="add"||t==="remove")&&i!==this)return;if(t==="destroy")this.remove(e,r);if(t==="change"){var n=this.modelId(e.previousAttributes());var s=this.modelId(e.attributes);if(n!==s){if(n!=null)delete this._byId[n];if(s!=null)this._byId[s]=e}}}this.trigger.apply(this,arguments)}});var S={forEach:3,each:3,map:3,collect:3,reduce:0,foldl:0,inject:0,reduceRight:0,foldr:0,find:3,detect:3,filter:3,select:3,reject:3,every:3,all:3,some:3,any:3,include:3,includes:3,contains:3,invoke:0,max:3,min:3,toArray:1,size:1,first:3,head:3,take:3,initial:3,rest:3,tail:3,drop:3,last:3,without:0,difference:0,indexOf:3,shuffle:1,lastIndexOf:3,isEmpty:1,chain:1,sample:3,partition:3,groupBy:3,countBy:3,sortBy:3,indexBy:3,findIndex:3,findLastIndex:3};h(x,S,"models");var k=e.View=function(t){this.cid=i.uniqueId("view");i.extend(this,i.pick(t,P));this._ensureElement();this.initialize.apply(this,arguments)};var T=/^(\S+)\s*(.*)$/;var P=["model","collection","el","id","attributes","className","tagName","events"];i.extend(k.prototype,u,{tagName:"div",$:function(t){return this.$el.find(t)},initialize:function(){},render:function(){return this},remove:function(){this._removeElement();this.stopListening();return this},_removeElement:function(){this.$el.remove()},setElement:function(t){this.undelegateEvents();this._setElement(t);this.delegateEvents();return this},_setElement:function(t){this.$el=t instanceof e.$?t:e.$(t);this.el=this.$el[0]},delegateEvents:function(t){t||(t=i.result(this,"events"));if(!t)return this;this.undelegateEvents();for(var e in t){var r=t[e];if(!i.isFunction(r))r=this[r];if(!r)continue;var n=e.match(T);this.delegate(n[1],n[2],i.bind(r,this))}return this},delegate:function(t,e,i){this.$el.on(t+".delegateEvents"+this.cid,e,i);return this},undelegateEvents:function(){if(this.$el)this.$el.off(".delegateEvents"+this.cid);return this},undelegate:function(t,e,i){this.$el.off(t+".delegateEvents"+this.cid,e,i);return this},_createElement:function(t){return document.createElement(t)},_ensureElement:function(){if(!this.el){var t=i.extend({},i.result(this,"attributes"));if(this.id)t.id=i.result(this,"id");if(this.className)t["class"]=i.result(this,"className");this.setElement(this._createElement(i.result(this,"tagName")));this._setAttributes(t)}else{this.setElement(i.result(this,"el"))}},_setAttributes:function(t){this.$el.attr(t)}});e.sync=function(t,r,n){var s=H[t];i.defaults(n||(n={}),{emulateHTTP:e.emulateHTTP,emulateJSON:e.emulateJSON});var a={type:s,dataType:"json"};if(!n.url){a.url=i.result(r,"url")||F()}if(n.data==null&&r&&(t==="create"||t==="update"||t==="patch")){a.contentType="application/json";a.data=JSON.stringify(n.attrs||r.toJSON(n))}if(n.emulateJSON){a.contentType="application/x-www-form-urlencoded";a.data=a.data?{model:a.data}:{}}if(n.emulateHTTP&&(s==="PUT"||s==="DELETE"||s==="PATCH")){a.type="POST";if(n.emulateJSON)a.data._method=s;var h=n.beforeSend;n.beforeSend=function(t){t.setRequestHeader("X-HTTP-Method-Override",s);if(h)return h.apply(this,arguments)}}if(a.type!=="GET"&&!n.emulateJSON){a.processData=false}var o=n.error;n.error=function(t,e,i){n.textStatus=e;n.errorThrown=i;if(o)o.call(n.context,t,e,i)};var l=n.xhr=e.ajax(i.extend(a,n));r.trigger("request",r,l,n);return l};var H={create:"POST",update:"PUT",patch:"PATCH","delete":"DELETE",read:"GET"};e.ajax=function(){return e.$.ajax.apply(e.$,arguments)};var $=e.Router=function(t){t||(t={});if(t.routes)this.routes=t.routes;this._bindRoutes();this.initialize.apply(this,arguments)};var A=/\((.*?)\)/g;var C=/(\(\?)?:\w+/g;var R=/\*\w+/g;var j=/[\-{}\[\]+?.,\\\^$|#\s]/g;i.extend($.prototype,u,{initialize:function(){},route:function(t,r,n){if(!i.isRegExp(t))t=this._routeToRegExp(t);if(i.isFunction(r)){n=r;r=""}if(!n)n=this[r];var s=this;e.history.route(t,function(i){var a=s._extractParameters(t,i);if(s.execute(n,a,r)!==false){s.trigger.apply(s,["route:"+r].concat(a));s.trigger("route",r,a);e.history.trigger("route",s,r,a)}});return this},execute:function(t,e,i){if(t)t.apply(this,e)},navigate:function(t,i){e.history.navigate(t,i);return this},_bindRoutes:function(){if(!this.routes)return;this.routes=i.result(this,"routes");var t,e=i.keys(this.routes);while((t=e.pop())!=null){this.route(t,this.routes[t])}},_routeToRegExp:function(t){t=t.replace(j,"\\$&").replace(A,"(?:$1)?").replace(C,function(t,e){return e?t:"([^/?]+)"}).replace(R,"([^?]*?)");return new RegExp("^"+t+"(?:\\?([\\s\\S]*))?$")},_extractParameters:function(t,e){var r=t.exec(e).slice(1);return i.map(r,function(t,e){if(e===r.length-1)return t||null;return t?decodeURIComponent(t):null})}});var N=e.History=function(){this.handlers=[];this.checkUrl=i.bind(this.checkUrl,this);if(typeof window!=="undefined"){this.location=window.location;this.history=window.history}};var M=/^[#\/]|\s+$/g;var O=/^\/+|\/+$/g;var U=/#.*$/;N.started=false;i.extend(N.prototype,u,{interval:50,atRoot:function(){var t=this.location.pathname.replace(/[^\/]$/,"$&/");return t===this.root&&!this.getSearch()},matchRoot:function(){var t=this.decodeFragment(this.location.pathname);var e=t.slice(0,this.root.length-1)+"/";return e===this.root},decodeFragment:function(t){return decodeURI(t.replace(/%25/g,"%2525"))},getSearch:function(){var t=this.location.href.replace(/#.*/,"").match(/\?.+/);return t?t[0]:""},getHash:function(t){var e=(t||this).location.href.match(/#(.*)$/);return e?e[1]:""},getPath:function(){var t=this.decodeFragment(this.location.pathname+this.getSearch()).slice(this.root.length-1);return t.charAt(0)==="/"?t.slice(1):t},getFragment:function(t){if(t==null){if(this._usePushState||!this._wantsHashChange){t=this.getPath()}else{t=this.getHash()}}return t.replace(M,"")},start:function(t){if(N.started)throw new Error("Backbone.history has already been started");N.started=true;this.options=i.extend({root:"/"},this.options,t);this.root=this.options.root;this._wantsHashChange=this.options.hashChange!==false;this._hasHashChange="onhashchange"in window&&(document.documentMode===void 0||document.documentMode>7);this._useHashChange=this._wantsHashChange&&this._hasHashChange;this._wantsPushState=!!this.options.pushState;this._hasPushState=!!(this.history&&this.history.pushState);this._usePushState=this._wantsPushState&&this._hasPushState;this.fragment=this.getFragment();this.root=("/"+this.root+"/").replace(O,"/");if(this._wantsHashChange&&this._wantsPushState){if(!this._hasPushState&&!this.atRoot()){var e=this.root.slice(0,-1)||"/";this.location.replace(e+"#"+this.getPath());return true}else if(this._hasPushState&&this.atRoot()){this.navigate(this.getHash(),{replace:true})}}if(!this._hasHashChange&&this._wantsHashChange&&!this._usePushState){this.iframe=document.createElement("iframe");this.iframe.src="javascript:0";this.iframe.style.display="none";this.iframe.tabIndex=-1;var r=document.body;var n=r.insertBefore(this.iframe,r.firstChild).contentWindow;n.document.open();n.document.close();n.location.hash="#"+this.fragment}var s=window.addEventListener||function(t,e){return attachEvent("on"+t,e)};if(this._usePushState){s("popstate",this.checkUrl,false)}else if(this._useHashChange&&!this.iframe){s("hashchange",this.checkUrl,false)}else if(this._wantsHashChange){this._checkUrlInterval=setInterval(this.checkUrl,this.interval)}if(!this.options.silent)return this.loadUrl()},stop:function(){var t=window.removeEventListener||function(t,e){return detachEvent("on"+t,e)};if(this._usePushState){t("popstate",this.checkUrl,false)}else if(this._useHashChange&&!this.iframe){t("hashchange",this.checkUrl,false)}if(this.iframe){document.body.removeChild(this.iframe);this.iframe=null}if(this._checkUrlInterval)clearInterval(this._checkUrlInterval);N.started=false},route:function(t,e){this.handlers.unshift({route:t,callback:e})},checkUrl:function(t){var e=this.getFragment();if(e===this.fragment&&this.iframe){e=this.getHash(this.iframe.contentWindow)}if(e===this.fragment)return false;if(this.iframe)this.navigate(e);this.loadUrl()},loadUrl:function(t){if(!this.matchRoot())return false;t=this.fragment=this.getFragment(t);return i.some(this.handlers,function(e){if(e.route.test(t)){e.callback(t);return true}})},navigate:function(t,e){if(!N.started)return false;if(!e||e===true)e={trigger:!!e};t=this.getFragment(t||"");var i=this.root;if(t===""||t.charAt(0)==="?"){i=i.slice(0,-1)||"/"}var r=i+t;t=this.decodeFragment(t.replace(U,""));if(this.fragment===t)return;this.fragment=t;if(this._usePushState){this.history[e.replace?"replaceState":"pushState"]({},document.title,r)}else if(this._wantsHashChange){this._updateHash(this.location,t,e.replace);if(this.iframe&&t!==this.getHash(this.iframe.contentWindow)){var n=this.iframe.contentWindow;if(!e.replace){n.document.open();n.document.close()}this._updateHash(n.location,t,e.replace)}}else{return this.location.assign(r)}if(e.trigger)return this.loadUrl(t)},_updateHash:function(t,e,i){if(i){var r=t.href.replace(/(javascript:|#).*$/,"");t.replace(r+"#"+e)}else{t.hash="#"+e}}});e.history=new N;var q=function(t,e){var r=this;var n;if(t&&i.has(t,"constructor")){n=t.constructor}else{n=function(){return r.apply(this,arguments)}}i.extend(n,r,e);n.prototype=i.create(r.prototype,t);n.prototype.constructor=n;n.__super__=r.prototype;return n};y.extend=x.extend=$.extend=k.extend=N.extend=q;var F=function(){throw new Error('A "url" property or function must be specified')};var B=function(t,e){var i=e.error;e.error=function(r){if(i)i.call(e.context,t,r,e);t.trigger("error",t,r,e)}};return e});

window.wp=window.wp||{},function(a){var b="undefined"==typeof _wpUtilSettings?{}:_wpUtilSettings;wp.template=_.memoize(function(b){var c,d={evaluate:/<#([\s\S]+?)#>/g,interpolate:/\{\{\{([\s\S]+?)\}\}\}/g,escape:/\{\{([^\}]+?)\}\}(?!\})/g,variable:"data"};return function(e){return(c=c||_.template(a("#tmpl-"+b).html(),d))(e)}}),wp.ajax={settings:b.ajax||{},post:function(a,b){return wp.ajax.send({data:_.isObject(a)?a:_.extend(b||{},{action:a})})},send:function(b,c){var d,e;return _.isObject(b)?c=b:(c=c||{},c.data=_.extend(c.data||{},{action:b})),c=_.defaults(c||{},{type:"POST",url:wp.ajax.settings.url,context:this}),e=a.Deferred(function(b){c.success&&b.done(c.success),c.error&&b.fail(c.error),delete c.success,delete c.error,b.jqXHR=a.ajax(c).done(function(a){"1"!==a&&1!==a||(a={success:!0}),_.isObject(a)&&!_.isUndefined(a.success)?b[a.success?"resolveWith":"rejectWith"](this,[a.data]):b.rejectWith(this,[a])}).fail(function(){b.rejectWith(this,arguments)})}),d=e.promise(),d.abort=function(){return e.jqXHR.abort(),this},d}}}(jQuery);
window.wp=window.wp||{},function(a){wp.Backbone={},wp.Backbone.Subviews=function(a,b){this.view=a,this._views=_.isArray(b)?{"":b}:b||{}},wp.Backbone.Subviews.extend=Backbone.Model.extend,_.extend(wp.Backbone.Subviews.prototype,{all:function(){return _.flatten(_.values(this._views))},get:function(a){return a=a||"",this._views[a]},first:function(a){var b=this.get(a);return b&&b.length?b[0]:null},set:function(a,b,c){var d,e;return _.isString(a)||(c=b,b=a,a=""),c=c||{},b=_.isArray(b)?b:[b],d=this.get(a),e=b,d&&(c.add?_.isUndefined(c.at)?e=d.concat(b):(e=d,e.splice.apply(e,[c.at,0].concat(b))):(_.each(e,function(a){a.__detach=!0}),_.each(d,function(a){a.__detach?a.$el.detach():a.remove()}),_.each(e,function(a){delete a.__detach}))),this._views[a]=e,_.each(b,function(b){var c=b.Views||wp.Backbone.Subviews,d=b.views=b.views||new c(b);d.parent=this.view,d.selector=a},this),c.silent||this._attach(a,b,_.extend({ready:this._isReady()},c)),this},add:function(a,b,c){return _.isString(a)||(c=b,b=a,a=""),this.set(a,b,_.extend({add:!0},c))},unset:function(a,b,c){var d;return _.isString(a)||(c=b,b=a,a=""),b=b||[],(d=this.get(a))&&(b=_.isArray(b)?b:[b],this._views[a]=b.length?_.difference(d,b):[]),c&&c.silent||_.invoke(b,"remove"),this},detach:function(){return a(_.pluck(this.all(),"el")).detach(),this},render:function(){var a={ready:this._isReady()};return _.each(this._views,function(b,c){this._attach(c,b,a)},this),this.rendered=!0,this},remove:function(a){return a&&a.silent||(this.parent&&this.parent.views&&this.parent.views.unset(this.selector,this.view,{silent:!0}),delete this.parent,delete this.selector),_.invoke(this.all(),"remove"),this._views=[],this},replace:function(a,b){return a.html(b),this},insert:function(a,b,c){var d,e=c&&c.at;return _.isNumber(e)&&(d=a.children()).length>e?d.eq(e).before(b):a.append(b),this},ready:function(){this.view.trigger("ready"),_.chain(this.all()).map(function(a){return a.views}).flatten().where({attached:!0}).invoke("ready")},_attach:function(a,b,c){var d,e=a?this.view.$(a):this.view.$el;return e.length?(d=_.chain(b).pluck("views").flatten().value(),_.each(d,function(a){a.rendered||(a.view.render(),a.rendered=!0)},this),this[c.add?"insert":"replace"](e,_.pluck(b,"el"),c),_.each(d,function(a){a.attached=!0,c.ready&&a.ready()},this),this):this},_isReady:function(){for(var a=this.view.el;a;){if(a===document.body)return!0;a=a.parentNode}return!1}}),wp.Backbone.View=Backbone.View.extend({Subviews:wp.Backbone.Subviews,constructor:function(a){this.views=new this.Subviews(this,this.views),this.on("ready",this.ready,this),this.options=a||{},Backbone.View.apply(this,arguments)},remove:function(){var a=Backbone.View.prototype.remove.apply(this,arguments);return this.views&&this.views.remove(),a},render:function(){var a;return this.prepare&&(a=this.prepare()),this.views.detach(),this.template&&(a=a||{},this.trigger("prepare",a),this.$el.html(this.template(a))),this.views.render(),this},prepare:function(){return this.options},ready:function(){}})}(jQuery);
!function(a){function b(d){if(c[d])return c[d].exports;var e=c[d]={i:d,l:!1,exports:{}};return a[d].call(e.exports,e,e.exports,b),e.l=!0,e.exports}var c={};return b.m=a,b.c=c,b.d=function(a,c,d){b.o(a,c)||Object.defineProperty(a,c,{configurable:!1,enumerable:!0,get:d})},b.n=function(a){var c=a&&a.__esModule?function(){return a["default"]}:function(){return a};return b.d(c,"a",c),c},b.o=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)},b.p="",b(b.s=20)}({20:function(a,b,c){var d,e,f,g,h=jQuery;window.wp=window.wp||{},g=wp.media=function(a){var b,c=g.view.MediaFrame;if(c)return a=_.defaults(a||{},{frame:"select"}),"select"===a.frame&&c.Select?b=new c.Select(a):"post"===a.frame&&c.Post?b=new c.Post(a):"manage"===a.frame&&c.Manage?b=new c.Manage(a):"image"===a.frame&&c.ImageDetails?b=new c.ImageDetails(a):"audio"===a.frame&&c.AudioDetails?b=new c.AudioDetails(a):"video"===a.frame&&c.VideoDetails?b=new c.VideoDetails(a):"edit-attachments"===a.frame&&c.EditAttachments&&(b=new c.EditAttachments(a)),delete a.frame,g.frame=b,b},_.extend(g,{model:{},view:{},controller:{},frames:{}}),f=g.model.l10n=window._wpMediaModelsL10n||{},g.model.settings=f.settings||{},delete f.settings,d=g.model.Attachment=c(21),e=g.model.Attachments=c(22),g.model.Query=c(23),g.model.PostImage=c(24),g.model.Selection=c(25),g.compare=function(a,b,c,d){return _.isEqual(a,b)?c===d?0:c>d?-1:1:a>b?-1:1},_.extend(g,{template:wp.template,post:wp.ajax.post,ajax:wp.ajax.send,fit:function(a){var b,c=a.width,d=a.height,e=a.maxWidth,f=a.maxHeight;return _.isUndefined(e)||_.isUndefined(f)?_.isUndefined(f)?b="width":_.isUndefined(e)&&d>f&&(b="height"):b=c/d>e/f?"width":"height","width"===b&&c>e?{width:e,height:Math.round(e*d/c)}:"height"===b&&d>f?{width:Math.round(f*c/d),height:f}:{width:c,height:d}},truncate:function(a,b,c){return b=b||30,c=c||"&hellip;",a.length<=b?a:a.substr(0,b/2)+c+a.substr(-1*b/2)}}),g.attachment=function(a){return d.get(a)},e.all=new e,g.query=function(a){return new e(null,{props:_.extend(_.defaults(a||{},{orderby:"date"}),{query:!0})})},h(window).on("unload",function(){window.wp=null})},21:function(a,b){var c,d=Backbone.$;c=Backbone.Model.extend({sync:function(a,b,c){return _.isUndefined(this.id)?d.Deferred().rejectWith(this).promise():"read"===a?(c=c||{},c.context=this,c.data=_.extend(c.data||{},{action:"get-attachment",id:this.id}),wp.media.ajax(c)):"update"===a?this.get("nonces")&&this.get("nonces").update?(c=c||{},c.context=this,c.data=_.extend(c.data||{},{action:"save-attachment",id:this.id,nonce:this.get("nonces").update,post_id:wp.media.model.settings.post.id}),b.hasChanged()&&(c.data.changes={},_.each(b.changed,function(a,b){c.data.changes[b]=this.get(b)},this)),wp.media.ajax(c)):d.Deferred().rejectWith(this).promise():"delete"===a?(c=c||{},c.wait||(this.destroyed=!0),c.context=this,c.data=_.extend(c.data||{},{action:"delete-post",id:this.id,_wpnonce:this.get("nonces")["delete"]}),wp.media.ajax(c).done(function(){this.destroyed=!0}).fail(function(){this.destroyed=!1})):Backbone.Model.prototype.sync.apply(this,arguments)},parse:function(a){return a?(a.date=new Date(a.date),a.modified=new Date(a.modified),a):a},saveCompat:function(a,b){var c=this;return this.get("nonces")&&this.get("nonces").update?wp.media.post("save-attachment-compat",_.defaults({id:this.id,nonce:this.get("nonces").update,post_id:wp.media.model.settings.post.id},a)).done(function(a,d,e){c.set(c.parse(a,e),b)}):d.Deferred().rejectWith(this).promise()}},{create:function(a){var b=wp.media.model.Attachments;return b.all.push(a)},get:_.memoize(function(a,b){var c=wp.media.model.Attachments;return c.all.push(b||{id:a})})}),a.exports=c},22:function(a,b){var c=Backbone.Collection.extend({model:wp.media.model.Attachment,initialize:function(a,b){b=b||{},this.props=new Backbone.Model,this.filters=b.filters||{},this.props.on("change",this._changeFilteredProps,this),this.props.on("change:order",this._changeOrder,this),this.props.on("change:orderby",this._changeOrderby,this),this.props.on("change:query",this._changeQuery,this),this.props.set(_.defaults(b.props||{})),b.observe&&this.observe(b.observe)},_changeOrder:function(){this.comparator&&this.sort()},_changeOrderby:function(a,b){this.comparator&&this.comparator!==c.comparator||(b&&"post__in"!==b?this.comparator=c.comparator:delete this.comparator)},_changeQuery:function(a,b){b?(this.props.on("change",this._requery,this),this._requery()):this.props.off("change",this._requery,this)},_changeFilteredProps:function(a){if(!this.props.get("query")){var b=_.chain(a.changed).map(function(b,d){var e=c.filters[d],f=a.get(d);if(e){if(f&&!this.filters[d])this.filters[d]=e;else{if(f||this.filters[d]!==e)return;delete this.filters[d]}return!0}},this).any().value();b&&(this._source||(this._source=new c(this.models)),this.reset(this._source.filter(this.validator,this)))}},validateDestroyed:!1,validator:function(a){return!(!_.isUndefined(a.attributes.context)&&""!==a.attributes.context)&&(!(!this.validateDestroyed&&a.destroyed)&&_.all(this.filters,function(b){return!!b.call(this,a)},this))},validate:function(a,b){var c=this.validator(a),d=!!this.get(a.cid);return!c&&d?this.remove(a,b):c&&!d&&this.add(a,b),this},validateAll:function(a,b){return b=b||{},_.each(a.models,function(a){this.validate(a,{silent:!0})},this),b.silent||this.trigger("reset",this,b),this},observe:function(a){return this.observers=this.observers||[],this.observers.push(a),a.on("add change remove",this._validateHandler,this),a.on("reset",this._validateAllHandler,this),this.validateAll(a),this},unobserve:function(a){return a?(a.off(null,null,this),this.observers=_.without(this.observers,a)):(_.each(this.observers,function(a){a.off(null,null,this)},this),delete this.observers),this},_validateHandler:function(a,b,c){return c=b===this.mirroring?c:{silent:c&&c.silent},this.validate(a,c)},_validateAllHandler:function(a,b){return this.validateAll(a,b)},mirror:function(a){return this.mirroring&&this.mirroring===a?this:(this.unmirror(),this.mirroring=a,this.reset([],{silent:!0}),this.observe(a),this)},unmirror:function(){this.mirroring&&(this.unobserve(this.mirroring),delete this.mirroring)},more:function(a){var b=jQuery.Deferred(),c=this.mirroring,d=this;return c&&c.more?(c.more(a).done(function(){this===d.mirroring&&b.resolveWith(this)}),b.promise()):b.resolveWith(this).promise()},hasMore:function(){return!!this.mirroring&&this.mirroring.hasMore()},parse:function(a,b){return _.isArray(a)||(a=[a]),_.map(a,function(a){var c,d,e;return a instanceof Backbone.Model?(c=a.get("id"),a=a.attributes):c=a.id,d=wp.media.model.Attachment.get(c),e=d.parse(a,b),_.isEqual(d.attributes,e)||d.set(e),d})},_requery:function(a){var b;this.props.get("query")&&(b=this.props.toJSON(),b.cache=!0!==a,this.mirror(wp.media.model.Query.get(b)))},saveMenuOrder:function(){if("menuOrder"===this.props.get("orderby")){var a=this.chain().filter(function(a){return!_.isUndefined(a.id)}).map(function(a,b){return b+=1,a.set("menuOrder",b),[a.id,b]}).object().value();if(!_.isEmpty(a))return wp.media.post("save-attachment-order",{nonce:wp.media.model.settings.post.nonce,post_id:wp.media.model.settings.post.id,attachments:a})}}},{comparator:function(a,b,c){var d=this.props.get("orderby"),e=this.props.get("order")||"DESC",f=a.cid,g=b.cid;return a=a.get(d),b=b.get(d),"date"!==d&&"modified"!==d||(a=a||new Date,b=b||new Date),c&&c.ties&&(f=g=null),"DESC"===e?wp.media.compare(a,b,f,g):wp.media.compare(b,a,g,f)},filters:{search:function(a){return!this.props.get("search")||_.any(["title","filename","description","caption","name"],function(b){var c=a.get(b);return c&&-1!==c.search(this.props.get("search"))},this)},type:function(a){var b,c,d=this.props.get("type"),e=a.toJSON();return!(d&&(!_.isArray(d)||d.length))||(b=e.mime||e.file&&e.file.type||"",c=_.isArray(d)?_.find(d,function(a){return-1!==b.indexOf(a)}):-1!==b.indexOf(d))},uploadedTo:function(a){var b=this.props.get("uploadedTo");return!!_.isUndefined(b)||b===a.get("uploadedTo")},status:function(a){var b=this.props.get("status");return!!_.isUndefined(b)||b===a.get("status")}}});a.exports=c},23:function(a,b){var c,d=wp.media.model.Attachments;c=d.extend({initialize:function(a,b){var c;b=b||{},d.prototype.initialize.apply(this,arguments),this.args=b.args,this._hasMore=!0,this.created=new Date,this.filters.order=function(a){var b=this.props.get("orderby"),c=this.props.get("order");return!this.comparator||(this.length?1!==this.comparator(a,this.last(),{ties:!0}):"DESC"!==c||"date"!==b&&"modified"!==b?"ASC"===c&&"menuOrder"===b&&0===a.get(b):a.get(b)>=this.created)},c=["s","order","orderby","posts_per_page","post_mime_type","post_parent","author"],wp.Uploader&&_(this.args).chain().keys().difference(c).isEmpty().value()&&this.observe(wp.Uploader.queue)},hasMore:function(){return this._hasMore},more:function(a){var b=this;return this._more&&"pending"===this._more.state()?this._more:this.hasMore()?(a=a||{},a.remove=!1,this._more=this.fetch(a).done(function(a){(_.isEmpty(a)||-1===this.args.posts_per_page||a.length<this.args.posts_per_page)&&(b._hasMore=!1)})):jQuery.Deferred().resolveWith(this).promise()},sync:function(a,b,c){var e,f;return"read"===a?(c=c||{},c.context=this,c.data=_.extend(c.data||{},{action:"query-attachments",post_id:wp.media.model.settings.post.id}),e=_.clone(this.args),-1!==e.posts_per_page&&(e.paged=Math.round(this.length/e.posts_per_page)+1),c.data.query=e,wp.media.ajax(c)):(f=d.prototype.sync?d.prototype:Backbone,f.sync.apply(this,arguments))}},{defaultProps:{orderby:"date",order:"DESC"},defaultArgs:{posts_per_page:40},orderby:{allowed:["name","author","date","title","modified","uploadedTo","id","post__in","menuOrder"],valuemap:{id:"ID",uploadedTo:"parent",menuOrder:"menu_order ID"}},propmap:{search:"s",type:"post_mime_type",perPage:"posts_per_page",menuOrder:"menu_order",uploadedTo:"post_parent",status:"post_status",include:"post__in",exclude:"post__not_in",author:"author"},get:function(){var a=[];return function(b,d){var e,f={},g=c.orderby,h=c.defaultProps,i=!!b.cache||_.isUndefined(b.cache);return delete b.query,delete b.cache,_.defaults(b,h),b.order=b.order.toUpperCase(),"DESC"!==b.order&&"ASC"!==b.order&&(b.order=h.order.toUpperCase()),_.contains(g.allowed,b.orderby)||(b.orderby=h.orderby),_.each(["include","exclude"],function(a){b[a]&&!_.isArray(b[a])&&(b[a]=[b[a]])}),_.each(b,function(a,b){_.isNull(a)||(f[c.propmap[b]||b]=a)}),_.defaults(f,c.defaultArgs),f.orderby=g.valuemap[b.orderby]||b.orderby,i?e=_.find(a,function(a){return _.isEqual(a.args,f)}):a=[],e||(e=new c([],_.extend(d||{},{props:b,args:f})),a.push(e)),e}}()}),a.exports=c},24:function(a,b){var c=Backbone.Model.extend({initialize:function(a){var b=wp.media.model.Attachment;this.attachment=!1,a.attachment_id&&(this.attachment=b.get(a.attachment_id),this.attachment.get("url")?(this.dfd=jQuery.Deferred(),this.dfd.resolve()):this.dfd=this.attachment.fetch(),this.bindAttachmentListeners()),this.on("change:link",this.updateLinkUrl,this),this.on("change:size",this.updateSize,this),this.setLinkTypeFromUrl(),this.setAspectRatio(),this.set("originalUrl",a.url)},bindAttachmentListeners:function(){this.listenTo(this.attachment,"sync",this.setLinkTypeFromUrl),this.listenTo(this.attachment,"sync",this.setAspectRatio),this.listenTo(this.attachment,"change",this.updateSize)},changeAttachment:function(a,b){this.stopListening(this.attachment),this.attachment=a,this.bindAttachmentListeners(),this.set("attachment_id",this.attachment.get("id")),this.set("caption",this.attachment.get("caption")),this.set("alt",this.attachment.get("alt")),this.set("size",b.get("size")),this.set("align",b.get("align")),this.set("link",b.get("link")),this.updateLinkUrl(),this.updateSize()},setLinkTypeFromUrl:function(){var a,b=this.get("linkUrl");return b?(a="custom",this.attachment?this.attachment.get("url")===b?a="file":this.attachment.get("link")===b&&(a="post"):this.get("url")===b&&(a="file"),void this.set("link",a)):void this.set("link","none")},updateLinkUrl:function(){var a,b=this.get("link");switch(b){case"file":a=this.attachment?this.attachment.get("url"):this.get("url"),this.set("linkUrl",a);break;case"post":this.set("linkUrl",this.attachment.get("link"));break;case"none":this.set("linkUrl","")}},updateSize:function(){var a;if(this.attachment){if("custom"===this.get("size"))return this.set("width",this.get("customWidth")),this.set("height",this.get("customHeight")),void this.set("url",this.get("originalUrl"));a=this.attachment.get("sizes")[this.get("size")],a&&(this.set("url",a.url),this.set("width",a.width),this.set("height",a.height))}},setAspectRatio:function(){var a;return this.attachment&&this.attachment.get("sizes")&&(a=this.attachment.get("sizes").full)?void this.set("aspectRatio",a.width/a.height):void this.set("aspectRatio",this.get("customWidth")/this.get("customHeight"))}});a.exports=c},25:function(a,b){var c,d=wp.media.model.Attachments;c=d.extend({initialize:function(a,b){d.prototype.initialize.apply(this,arguments),this.multiple=b&&b.multiple,this.on("add remove reset",_.bind(this.single,this,!1))},add:function(a,b){return this.multiple||this.remove(this.models),d.prototype.add.call(this,a,b)},single:function(a){var b=this._single;return a&&(this._single=a),this._single&&!this.get(this._single.cid)&&delete this._single,this._single=this._single||this.last(),this._single!==b&&(b&&(b.trigger("selection:unsingle",b,this),this.get(b.cid)||this.trigger("selection:unsingle",b,this)),this._single&&this._single.trigger("selection:single",this._single,this)),this._single}}),a.exports=c}});
window.wp=window.wp||{},function(a,b){var c;"undefined"!=typeof _wpPluploadSettings&&(c=function(a){var d,e,f=this,g=navigator.userAgent.indexOf("Trident/")!=-1||navigator.userAgent.indexOf("MSIE ")!=-1,h={container:"container",browser:"browse_button",dropzone:"drop_element"};if(this.supports={upload:c.browser.supported},this.supported=this.supports.upload,this.supported){this.plupload=b.extend(!0,{multipart_params:{}},c.defaults),this.container=document.body,b.extend(!0,this,a);for(d in this)b.isFunction(this[d])&&(this[d]=b.proxy(this[d],this));for(d in h)this[d]&&(this[d]=b(this[d]).first(),this[d].length?(this[d].prop("id")||this[d].prop("id","__wp-uploader-id-"+c.uuid++),this.plupload[h[d]]=this[d].prop("id")):delete this[d]);(this.browser&&this.browser.length||this.dropzone&&this.dropzone.length)&&(g||"flash"!==plupload.predictRuntime(this.plupload)||this.plupload.required_features&&this.plupload.required_features.hasOwnProperty("send_binary_string")||(this.plupload.required_features=this.plupload.required_features||{},this.plupload.required_features.send_binary_string=!0),this.uploader=new plupload.Uploader(this.plupload),delete this.plupload,this.param(this.params||{}),delete this.params,e=function(a,b,d){d.attachment&&d.attachment.destroy(),c.errors.unshift({message:a||pluploadL10n.default_error,data:b,file:d}),f.error(a,b,d)},this.uploader.bind("init",function(a){var d,e,g,h=f.dropzone;if(g=f.supports.dragdrop=a.features.dragdrop&&!c.browser.mobile,h){if(h.toggleClass("supports-drag-drop",!!g),!g)return h.unbind(".wp-uploader");h.bind("dragover.wp-uploader",function(){d&&clearTimeout(d),e||(h.trigger("dropzone:enter").addClass("drag-over"),e=!0)}),h.bind("dragleave.wp-uploader, drop.wp-uploader",function(){d=setTimeout(function(){e=!1,h.trigger("dropzone:leave").removeClass("drag-over")},0)}),f.ready=!0,b(f).trigger("uploader:ready")}}),this.uploader.bind("postinit",function(a){a.refresh(),f.init()}),this.uploader.init(),this.browser?this.browser.on("mouseenter",this.refresh):(this.uploader.disableBrowse(!0),b("#"+this.uploader.id+"_html5_container").hide()),this.uploader.bind("FilesAdded",function(a,b){_.each(b,function(a){var b,d;plupload.FAILED!==a.status&&(b=_.extend({file:a,uploading:!0,date:new Date,filename:a.name,menuOrder:0,uploadedTo:wp.media.model.settings.post.id},_.pick(a,"loaded","size","percent")),d=/(?:jpe?g|png|gif)$/i.exec(a.name),d&&(b.type="image",b.subtype="jpg"===d[0]?"jpeg":d[0]),a.attachment=wp.media.model.Attachment.create(b),c.queue.add(a.attachment),f.added(a.attachment))}),a.refresh(),a.start()}),this.uploader.bind("UploadProgress",function(a,b){b.attachment.set(_.pick(b,"loaded","percent")),f.progress(b.attachment)}),this.uploader.bind("FileUploaded",function(a,b,d){var g;try{d=JSON.parse(d.response)}catch(h){return e(pluploadL10n.default_error,h,b)}return!_.isObject(d)||_.isUndefined(d.success)?e(pluploadL10n.default_error,null,b):d.success?(_.each(["file","loaded","size","percent"],function(a){b.attachment.unset(a)}),b.attachment.set(_.extend(d.data,{uploading:!1})),wp.media.model.Attachment.get(d.data.id,b.attachment),g=c.queue.all(function(a){return!a.get("uploading")}),g&&c.queue.reset(),void f.success(b.attachment)):e(d.data&&d.data.message,d.data,b)}),this.uploader.bind("Error",function(a,b){var d,f=pluploadL10n.default_error;for(d in c.errorMap)if(b.code===plupload[d]){f=c.errorMap[d],_.isFunction(f)&&(f=f(b.file,b));break}e(f,b,b.file),a.refresh()}))}},b.extend(c,_wpPluploadSettings),c.uuid=0,c.errorMap={FAILED:pluploadL10n.upload_failed,FILE_EXTENSION_ERROR:pluploadL10n.invalid_filetype,IMAGE_FORMAT_ERROR:pluploadL10n.not_an_image,IMAGE_MEMORY_ERROR:pluploadL10n.image_memory_exceeded,IMAGE_DIMENSIONS_ERROR:pluploadL10n.image_dimensions_exceeded,GENERIC_ERROR:pluploadL10n.upload_failed,IO_ERROR:pluploadL10n.io_error,HTTP_ERROR:pluploadL10n.http_error,SECURITY_ERROR:pluploadL10n.security_error,FILE_SIZE_ERROR:function(a){return pluploadL10n.file_exceeds_size_limit.replace("%s",a.name)}},b.extend(c.prototype,{param:function(a,c){return 1===arguments.length&&"string"==typeof a?this.uploader.settings.multipart_params[a]:void(arguments.length>1?this.uploader.settings.multipart_params[a]=c:b.extend(this.uploader.settings.multipart_params,a))},init:function(){},error:function(){},success:function(){},added:function(){},progress:function(){},complete:function(){},refresh:function(){var a,c,d,e;if(this.browser){for(a=this.browser[0];a;){if(a===document.body){c=!0;break}a=a.parentNode}c||(e="wp-uploader-browser-"+this.uploader.id,d=b("#"+e),d.length||(d=b('<div class="wp-uploader-browser" />').css({position:"fixed",top:"-1000px",left:"-1000px",height:0,width:0}).attr("id","wp-uploader-browser-"+this.uploader.id).appendTo("body")),d.append(this.browser))}this.uploader.refresh()}}),c.queue=new wp.media.model.Attachments([],{query:!1}),c.errors=new Backbone.Collection,a.Uploader=c)}(wp,jQuery);
!function(a,b){function c(){function a(){"undefined"!=typeof _wpmejsSettings&&(c=b.extend(!0,{},_wpmejsSettings)),c.classPrefix="mejs-",c.success=c.success||function(a){var b,c;a.rendererName&&-1!==a.rendererName.indexOf("flash")&&(b=a.attributes.autoplay&&"false"!==a.attributes.autoplay,c=a.attributes.loop&&"false"!==a.attributes.loop,b&&a.addEventListener("canplay",function(){a.play()},!1),c&&a.addEventListener("ended",function(){a.play()},!1))},c.customError=function(a,b){if(-1!==a.rendererName.indexOf("flash")||-1!==a.rendererName.indexOf("flv"))return'<a href="'+b.src+'">'+mejsL10n.strings["mejs.download-video"]+"</a>"},b(".wp-audio-shortcode, .wp-video-shortcode").not(".mejs-container").filter(function(){return!b(this).parent().hasClass("mejs-mediaelement")}).mediaelementplayer(c)}var c={};return{initialize:a}}a.wp=a.wp||{},a.wp.mediaelement=new c,b(a.wp.mediaelement.initialize)}(window,jQuery);
!function(a){function b(a){return a=b.buildAjaxOptions(a),b.transport(a)}var c=window.wpApiSettings;b.buildAjaxOptions=function(b){var d,e,f,g,h,i=b.url,j=b.path;if("string"==typeof b.namespace&&"string"==typeof b.endpoint&&(d=b.namespace.replace(/^\/|\/$/g,""),e=b.endpoint.replace(/^\//,""),j=e?d+"/"+e:d),"string"==typeof j&&(i=c.root+j.replace(/^\//,"")),g=!(b.data&&b.data._wpnonce),f=b.headers||{},g)for(h in f)if(f.hasOwnProperty(h)&&"x-wp-nonce"===h.toLowerCase()){g=!1;break}return g&&(f=a.extend({"X-WP-Nonce":c.nonce},f)),b=a.extend({},b,{headers:f,url:i}),delete b.path,delete b.namespace,delete b.endpoint,b},b.transport=a.ajax,window.wp=window.wp||{},window.wp.apiRequest=b}(jQuery);
!function(a){function b(d){if(c[d])return c[d].exports;var e=c[d]={i:d,l:!1,exports:{}};return a[d].call(e.exports,e,e.exports,b),e.l=!0,e.exports}var c={};return b.m=a,b.c=c,b.d=function(a,c,d){b.o(a,c)||Object.defineProperty(a,c,{configurable:!1,enumerable:!0,get:d})},b.n=function(a){var c=a&&a.__esModule?function(){return a["default"]}:function(){return a};return b.d(c,"a",c),c},b.o=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)},b.p="",b(b.s=26)}
//media-views.js
(Array(26).concat([function(a,b,c){var d,e=wp.media,f=jQuery;e.isTouchDevice="ontouchend"in document,d=e.view.l10n=window._wpMediaViewsL10n||{},e.view.settings=d.settings||{},delete d.settings,e.model.settings.post=e.view.settings.post,f.support.transition=function(){var a,b=document.documentElement.style,c={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};return a=_.find(_.keys(c),function(a){return!_.isUndefined(b[a])}),a&&{end:c[a]}}(),e.events=_.extend({},Backbone.Events),e.transition=function(a,b){var c=f.Deferred();return b=b||2e3,f.support.transition?(a instanceof f||(a=f(a)),a.first().one(f.support.transition.end,c.resolve),_.delay(c.resolve,b)):c.resolve(),c.promise()},e.controller.Region=c(27),e.controller.StateMachine=c(28),e.controller.State=c(29),e.selectionSync=c(30),e.controller.Library=c(31),e.controller.ImageDetails=c(32),e.controller.GalleryEdit=c(33),e.controller.GalleryAdd=c(34),e.controller.CollectionEdit=c(35),e.controller.CollectionAdd=c(36),e.controller.FeaturedImage=c(37),e.controller.ReplaceImage=c(38),e.controller.EditImage=c(39),e.controller.MediaLibrary=c(40),e.controller.Embed=c(41),e.controller.Cropper=c(42),e.controller.CustomizeImageCropper=c(43),e.controller.SiteIconCropper=c(44),e.View=c(45),e.view.Frame=c(46),e.view.MediaFrame=c(47),e.view.MediaFrame.Select=c(48),e.view.MediaFrame.Post=c(49),e.view.MediaFrame.ImageDetails=c(50),e.view.Modal=c(51),e.view.FocusManager=c(52),e.view.UploaderWindow=c(53),e.view.EditorUploader=c(54),e.view.UploaderInline=c(55),e.view.UploaderStatus=c(56),e.view.UploaderStatusError=c(57),e.view.Toolbar=c(58),e.view.Toolbar.Select=c(59),e.view.Toolbar.Embed=c(60),e.view.Button=c(61),e.view.ButtonGroup=c(62),e.view.PriorityList=c(63),e.view.MenuItem=c(64),e.view.Menu=c(65),e.view.RouterItem=c(66),e.view.Router=c(67),e.view.Sidebar=c(68),e.view.Attachment=c(69),e.view.Attachment.Library=c(70),e.view.Attachment.EditLibrary=c(71),e.view.Attachments=c(72),e.view.Search=c(73),e.view.AttachmentFilters=c(74),e.view.DateFilter=c(75),e.view.AttachmentFilters.Uploaded=c(76),e.view.AttachmentFilters.All=c(77),e.view.AttachmentsBrowser=c(78),e.view.Selection=c(79),e.view.Attachment.Selection=c(80),e.view.Attachments.Selection=c(81),e.view.Attachment.EditSelection=c(82),e.view.Settings=c(83),e.view.Settings.AttachmentDisplay=c(84),e.view.Settings.Gallery=c(85),e.view.Settings.Playlist=c(86),e.view.Attachment.Details=c(87),e.view.AttachmentCompat=c(88),e.view.Iframe=c(89),e.view.Embed=c(90),e.view.Label=c(91),e.view.EmbedUrl=c(92),e.view.EmbedLink=c(93),e.view.EmbedImage=c(94),e.view.ImageDetails=c(95),e.view.Cropper=c(96),e.view.SiteIconCropper=c(97),e.view.SiteIconPreview=c(98),e.view.EditImage=c(99),e.view.Spinner=c(100)},function(a,b){var c=function(a){_.extend(this,_.pick(a||{},"id","view","selector"))};c.extend=Backbone.Model.extend,_.extend(c.prototype,{mode:function(a){return a?a===this._mode?this:(this.trigger("deactivate"),this._mode=a,this.render(a),this.trigger("activate"),this):this._mode},render:function(a){if(a&&a!==this._mode)return this.mode(a);var b,c={view:null};return this.trigger("create",c),b=c.view,this.trigger("render",b),b&&this.set(b),this},get:function(){return this.view.views.first(this.selector)},set:function(a,b){return b&&(b.add=!1),this.view.views.set(this.selector,a,b)},trigger:function(a){var b,c;if(this._mode)return c=_.toArray(arguments),b=this.id+":"+a,c[0]=b+":"+this._mode,this.view.trigger.apply(this.view,c),c[0]=b,this.view.trigger.apply(this.view,c),this}}),a.exports=c},function(a,b){var c=function(a){this.states=new Backbone.Collection(a)};c.extend=Backbone.Model.extend,_.extend(c.prototype,Backbone.Events,{state:function(a){return this.states=this.states||new Backbone.Collection,a=a||this._state,a&&!this.states.get(a)&&this.states.add({id:a}),this.states.get(a)},setState:function(a){var b=this.state();return b&&a===b.id||!this.states||!this.states.get(a)?this:(b&&(b.trigger("deactivate"),this._lastState=b.id),this._state=a,this.state().trigger("activate"),this)},lastState:function(){if(this._lastState)return this.state(this._lastState)}}),_.each(["on","off","trigger"],function(a){c.prototype[a]=function(){return this.states=this.states||new Backbone.Collection,this.states[a].apply(this.states,arguments),this}}),a.exports=c},function(a,b){var c=Backbone.Model.extend({constructor:function(){this.on("activate",this._preActivate,this),this.on("activate",this.activate,this),this.on("activate",this._postActivate,this),this.on("deactivate",this._deactivate,this),this.on("deactivate",this.deactivate,this),this.on("reset",this.reset,this),this.on("ready",this._ready,this),this.on("ready",this.ready,this),Backbone.Model.apply(this,arguments),this.on("change:menu",this._updateMenu,this)},ready:function(){},activate:function(){},deactivate:function(){},reset:function(){},_ready:function(){this._updateMenu()},_preActivate:function(){this.active=!0},_postActivate:function(){this.on("change:menu",this._menu,this),this.on("change:titleMode",this._title,this),this.on("change:content",this._content,this),this.on("change:toolbar",this._toolbar,this),this.frame.on("title:render:default",this._renderTitle,this),this._title(),this._menu(),this._toolbar(),this._content(),this._router()},_deactivate:function(){this.active=!1,this.frame.off("title:render:default",this._renderTitle,this),this.off("change:menu",this._menu,this),this.off("change:titleMode",this._title,this),this.off("change:content",this._content,this),this.off("change:toolbar",this._toolbar,this)},_title:function(){this.frame.title.render(this.get("titleMode")||"default")},_renderTitle:function(a){a.$el.text(this.get("title")||"")},_router:function(){var a,b=this.frame.router,c=this.get("router");this.frame.$el.toggleClass("hide-router",!c),c&&(this.frame.router.render(c),a=b.get(),a&&a.select&&a.select(this.frame.content.mode()))},_menu:function(){var a,b=this.frame.menu,c=this.get("menu");this.frame.$el.toggleClass("hide-menu",!c),c&&(b.mode(c),a=b.get(),a&&a.select&&a.select(this.id))},_updateMenu:function(){var a=this.previous("menu"),b=this.get("menu");a&&this.frame.off("menu:render:"+a,this._renderMenu,this),b&&this.frame.on("menu:render:"+b,this._renderMenu,this)},_renderMenu:function(a){var b=this.get("menuItem"),c=this.get("title"),d=this.get("priority");!b&&c&&(b={text:c},d&&(b.priority=d)),b&&a.set(this.id,b)}});_.each(["toolbar","content"],function(a){c.prototype["_"+a]=function(){var b=this.get(a);b&&this.frame[a].render(b)}}),a.exports=c},function(a,b){var c={syncSelection:function(){var a=this.get("selection"),b=this.frame._selection;this.get("syncSelection")&&b&&a&&(a.multiple&&(a.reset([],{silent:!0}),a.validateAll(b.attachments),b.difference=_.difference(b.attachments.models,a.models)),a.single(b.single))},recordSelection:function(){var a=this.get("selection"),b=this.frame._selection;this.get("syncSelection")&&b&&a&&(a.multiple?(b.attachments.reset(a.toArray().concat(b.difference)),b.difference=[]):b.attachments.add(a.toArray()),b.single=a._single)}};a.exports=c},function(a,b){var c,d=wp.media.view.l10n,e=window.getUserSetting,f=window.setUserSetting;c=wp.media.controller.State.extend({defaults:{id:"library",title:d.mediaLibraryTitle,multiple:!1,content:"upload",menu:"default",router:"browse",toolbar:"select",searchable:!0,filterable:!1,sortable:!0,autoSelect:!0,describe:!1,contentUserSetting:!0,syncSelection:!0},initialize:function(){var a,b=this.get("selection");this.get("library")||this.set("library",wp.media.query()),b instanceof wp.media.model.Selection||(a=b,a||(a=this.get("library").props.toJSON(),a=_.omit(a,"orderby","query")),this.set("selection",new wp.media.model.Selection(null,{multiple:this.get("multiple"),props:a}))),this.resetDisplays()},activate:function(){this.syncSelection(),wp.Uploader.queue.on("add",this.uploading,this),this.get("selection").on("add remove reset",this.refreshContent,this),this.get("router")&&this.get("contentUserSetting")&&(this.frame.on("content:activate",this.saveContentMode,this),this.set("content",e("libraryContent",this.get("content"))))},deactivate:function(){this.recordSelection(),this.frame.off("content:activate",this.saveContentMode,this),this.get("selection").off(null,null,this),wp.Uploader.queue.off(null,null,this)},reset:function(){this.get("selection").reset(),this.resetDisplays(),this.refreshContent()},resetDisplays:function(){var a=wp.media.view.settings.defaultProps;this._displays=[],this._defaultDisplaySettings={align:e("align",a.align)||"none",size:e("imgsize",a.size)||"medium",link:e("urlbutton",a.link)||"none"}},display:function(a){var b=this._displays;return b[a.cid]||(b[a.cid]=new Backbone.Model(this.defaultDisplaySettings(a))),b[a.cid]},defaultDisplaySettings:function(a){var b=_.clone(this._defaultDisplaySettings);return(b.canEmbed=this.canEmbed(a))?b.link="embed":this.isImageAttachment(a)||"none"!==b.link||(b.link="file"),b},isImageAttachment:function(a){return a.get("uploading")?/\.(jpe?g|png|gif)$/i.test(a.get("filename")):"image"===a.get("type")},canEmbed:function(a){if(!a.get("uploading")){var b=a.get("type");if("audio"!==b&&"video"!==b)return!1}return _.contains(wp.media.view.settings.embedExts,a.get("filename").split(".").pop())},refreshContent:function(){var a=this.get("selection"),b=this.frame,c=b.router.get(),d=b.content.mode();this.active&&!a.length&&c&&!c.get(d)&&this.frame.content.render(this.get("content"))},uploading:function(a){var b=this.frame.content;"upload"===b.mode()&&this.frame.content.mode("browse"),this.get("autoSelect")&&(this.get("selection").add(a),this.frame.trigger("library:selection:add"))},saveContentMode:function(){if("browse"===this.get("router")){var a=this.frame.content.mode(),b=this.frame.router.get();b&&b.get(a)&&f("libraryContent",a)}}}),_.extend(c.prototype,wp.media.selectionSync),a.exports=c},function(a,b){var c,d=wp.media.controller.State,e=wp.media.controller.Library,f=wp.media.view.l10n;c=d.extend({defaults:_.defaults({id:"image-details",title:f.imageDetailsTitle,content:"image-details",menu:!1,router:!1,toolbar:"image-details",editing:!1,priority:60},e.prototype.defaults),initialize:function(a){this.image=a.image,d.prototype.initialize.apply(this,arguments)},activate:function(){this.frame.modal.$el.addClass("image-details")}}),a.exports=c},function(a,b){var c,d=wp.media.controller.Library,e=wp.media.view.l10n;c=d.extend({defaults:{id:"gallery-edit",title:e.editGalleryTitle,multiple:!1,searchable:!1,sortable:!0,date:!1,display:!1,content:"browse",toolbar:"gallery-edit",describe:!0,displaySettings:!0,dragInfo:!0,idealColumnWidth:170,editing:!1,priority:60,syncSelection:!1},initialize:function(){this.get("library")||this.set("library",new wp.media.model.Selection),this.get("AttachmentView")||this.set("AttachmentView",wp.media.view.Attachment.EditLibrary),d.prototype.initialize.apply(this,arguments)},activate:function(){var a=this.get("library");a.props.set("type","image"),this.get("library").observe(wp.Uploader.queue),this.frame.on("content:render:browse",this.gallerySettings,this),d.prototype.activate.apply(this,arguments)},deactivate:function(){this.get("library").unobserve(wp.Uploader.queue),this.frame.off("content:render:browse",this.gallerySettings,this),d.prototype.deactivate.apply(this,arguments)},gallerySettings:function(a){if(this.get("displaySettings")){var b=this.get("library");b&&a&&(b.gallery=b.gallery||new Backbone.Model,a.sidebar.set({gallery:new wp.media.view.Settings.Gallery({controller:this,model:b.gallery,priority:40})}),a.toolbar.set("reverse",{text:e.reverseOrder,priority:80,click:function(){b.reset(b.toArray().reverse())}}))}}}),a.exports=c},function(a,b){var c,d=wp.media.model.Selection,e=wp.media.controller.Library,f=wp.media.view.l10n;c=e.extend({defaults:_.defaults({id:"gallery-library",title:f.addToGalleryTitle,multiple:"add",filterable:"uploaded",menu:"gallery",toolbar:"gallery-add",priority:100,syncSelection:!1},e.prototype.defaults),initialize:function(){this.get("library")||this.set("library",wp.media.query({type:"image"})),e.prototype.initialize.apply(this,arguments)},activate:function(){var a=this.get("library"),b=this.frame.state("gallery-edit").get("library");this.editLibrary&&this.editLibrary!==b&&a.unobserve(this.editLibrary),a.validator=function(a){return!!this.mirroring.get(a.cid)&&!b.get(a.cid)&&d.prototype.validator.apply(this,arguments)},a.reset(a.mirroring.models,{silent:!0}),a.observe(b),this.editLibrary=b,e.prototype.activate.apply(this,arguments)}}),a.exports=c},function(a,b){var c,d=wp.media.controller.Library,e=wp.media.view.l10n,f=jQuery;c=d.extend({defaults:{multiple:!1,sortable:!0,date:!1,searchable:!1,content:"browse",describe:!0,dragInfo:!0,idealColumnWidth:170,editing:!1,priority:60,SettingsView:!1,syncSelection:!1},initialize:function(){var a=this.get("collectionType");"video"===this.get("type")&&(a="video-"+a),this.set("id",a+"-edit"),this.set("toolbar",a+"-edit"),this.get("library")||this.set("library",new wp.media.model.Selection),this.get("AttachmentView")||this.set("AttachmentView",wp.media.view.Attachment.EditLibrary),d.prototype.initialize.apply(this,arguments)},activate:function(){var a=this.get("library");a.props.set("type",this.get("type")),this.get("library").observe(wp.Uploader.queue),this.frame.on("content:render:browse",this.renderSettings,this),d.prototype.activate.apply(this,arguments)},deactivate:function(){this.get("library").unobserve(wp.Uploader.queue),this.frame.off("content:render:browse",this.renderSettings,this),d.prototype.deactivate.apply(this,arguments)},renderSettings:function(a){var b=this.get("library"),c=this.get("collectionType"),d=this.get("dragInfoText"),g=this.get("SettingsView"),h={};b&&a&&(b[c]=b[c]||new Backbone.Model,h[c]=new g({controller:this,model:b[c],priority:40}),a.sidebar.set(h),d&&a.toolbar.set("dragInfo",new wp.media.View({el:f('<div class="instructions">'+d+"</div>")[0],priority:-40})),a.toolbar.set("reverse",{text:e.reverseOrder,priority:80,click:function(){b.reset(b.toArray().reverse())}}))}}),a.exports=c},function(a,b){var c,d=wp.media.model.Selection,e=wp.media.controller.Library;c=e.extend({defaults:_.defaults({multiple:"add",filterable:"uploaded",priority:100,syncSelection:!1},e.prototype.defaults),initialize:function(){var a=this.get("collectionType");"video"===this.get("type")&&(a="video-"+a),this.set("id",a+"-library"),this.set("toolbar",a+"-add"),this.set("menu",a),this.get("library")||this.set("library",wp.media.query({type:this.get("type")})),e.prototype.initialize.apply(this,arguments)},activate:function(){var a=this.get("library"),b=this.get("editLibrary"),c=this.frame.state(this.get("collectionType")+"-edit").get("library");b&&b!==c&&a.unobserve(b),a.validator=function(a){return!!this.mirroring.get(a.cid)&&!c.get(a.cid)&&d.prototype.validator.apply(this,arguments)},a.reset(a.mirroring.models,{silent:!0}),a.observe(c),this.set("editLibrary",c),e.prototype.activate.apply(this,arguments)}}),a.exports=c},function(a,b){var c,d=wp.media.model.Attachment,e=wp.media.controller.Library,f=wp.media.view.l10n;c=e.extend({defaults:_.defaults({id:"featured-image",title:f.setFeaturedImageTitle,multiple:!1,filterable:"uploaded",toolbar:"featured-image",priority:60,syncSelection:!0},e.prototype.defaults),initialize:function(){var a,b;this.get("library")||this.set("library",wp.media.query({type:"image"})),e.prototype.initialize.apply(this,arguments),a=this.get("library"),b=a.comparator,a.comparator=function(a,c){var d=!!this.mirroring.get(a.cid),e=!!this.mirroring.get(c.cid);return!d&&e?-1:d&&!e?1:b.apply(this,arguments)},a.observe(this.get("selection"))},activate:function(){this.updateSelection(),this.frame.on("open",this.updateSelection,this),e.prototype.activate.apply(this,arguments)},deactivate:function(){this.frame.off("open",this.updateSelection,this),e.prototype.deactivate.apply(this,arguments)},updateSelection:function(){var a,b=this.get("selection"),c=wp.media.view.settings.post.featuredImageId;""!==c&&-1!==c&&(a=d.get(c),a.fetch()),b.reset(a?[a]:[])}}),a.exports=c},function(a,b){var c,d=wp.media.controller.Library,e=wp.media.view.l10n;c=d.extend({defaults:_.defaults({id:"replace-image",title:e.replaceImageTitle,multiple:!1,filterable:"uploaded",toolbar:"replace",menu:!1,priority:60,syncSelection:!0},d.prototype.defaults),initialize:function(a){var b,c;this.image=a.image,this.get("library")||this.set("library",wp.media.query({type:"image"})),d.prototype.initialize.apply(this,arguments),b=this.get("library"),c=b.comparator,b.comparator=function(a,b){var d=!!this.mirroring.get(a.cid),e=!!this.mirroring.get(b.cid);return!d&&e?-1:d&&!e?1:c.apply(this,arguments)},b.observe(this.get("selection"))},activate:function(){this.updateSelection(),d.prototype.activate.apply(this,arguments)},updateSelection:function(){var a=this.get("selection"),b=this.image.attachment;a.reset(b?[b]:[])}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.controller.State.extend({defaults:{id:"edit-image",title:d.editImage,menu:!1,toolbar:"edit-image",content:"edit-image",url:""},activate:function(){this.frame.on("toolbar:render:edit-image",_.bind(this.toolbar,this))},deactivate:function(){this.frame.off("toolbar:render:edit-image")},toolbar:function(){var a=this.frame,b=a.lastState(),c=b&&b.id;a.toolbar.set(new wp.media.view.Toolbar({controller:a,items:{back:{style:"primary",text:d.back,priority:20,click:function(){c?a.setState(c):a.close()}}}}))}}),a.exports=c},function(a,b){var c,d=wp.media.controller.Library;c=d.extend({defaults:_.defaults({filterable:"uploaded",displaySettings:!1,priority:80,syncSelection:!1},d.prototype.defaults),initialize:function(a){this.media=a.media,this.type=a.type,this.set("library",wp.media.query({type:this.type})),d.prototype.initialize.apply(this,arguments)},activate:function(){wp.media.frame.lastMime&&(this.set("library",wp.media.query({type:wp.media.frame.lastMime})),delete wp.media.frame.lastMime),d.prototype.activate.apply(this,arguments)}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n,e=Backbone.$;c=wp.media.controller.State.extend({defaults:{id:"embed",title:d.insertFromUrlTitle,content:"embed",menu:"default",toolbar:"main-embed",priority:120,type:"link",url:"",metadata:{}},sensitivity:400,initialize:function(a){this.metadata=a.metadata,this.debouncedScan=_.debounce(_.bind(this.scan,this),this.sensitivity),this.props=new Backbone.Model(this.metadata||{url:""}),this.props.on("change:url",this.debouncedScan,this),this.props.on("change:url",this.refresh,this),this.on("scan",this.scanImage,this)},scan:function(){var a,b=this,c={type:"link",scanners:[]};this.props.get("url")&&this.trigger("scan",c),c.scanners.length?(a=c.scanners=e.when.apply(e,c.scanners),a.always(function(){b.get("scanners")===a&&b.set("loading",!1)})):c.scanners=null,c.loading=!!c.scanners,this.set(c)},scanImage:function(a){var b=this.frame,c=this,d=this.props.get("url"),f=new Image,g=e.Deferred();a.scanners.push(g.promise()),f.onload=function(){g.resolve(),c===b.state()&&d===c.props.get("url")&&(c.set({type:"image"}),c.props.set({width:f.width,height:f.height}))},f.onerror=g.reject,f.src=d},refresh:function(){this.frame.toolbar.get().refresh()},reset:function(){this.props.clear().set({url:""}),this.active&&this.refresh()}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.controller.State.extend({defaults:{id:"cropper",title:d.cropImage,toolbar:"crop",content:"crop",router:!1,canSkipCrop:!1,doCropArgs:{}},activate:function(){this.frame.on("content:create:crop",this.createCropContent,this),this.frame.on("close",this.removeCropper,this),this.set("selection",new Backbone.Collection(this.frame._selection.single))},deactivate:function(){this.frame.toolbar.mode("browse")},createCropContent:function(){this.cropperView=new wp.media.view.Cropper({controller:this,attachment:this.get("selection").first()}),this.cropperView.on("image-loaded",this.createCropToolbar,this),this.frame.content.set(this.cropperView)},removeCropper:function(){this.imgSelect.cancelSelection(),this.imgSelect.setOptions({remove:!0}),this.imgSelect.update(),this.cropperView.remove()},createCropToolbar:function(){var a,b;a=this.get("canSkipCrop")||!1,b={controller:this.frame,items:{insert:{style:"primary",text:d.cropImage,priority:80,requires:{library:!1,selection:!1},click:function(){var a,b=this.controller;a=b.state().get("selection").first(),a.set({cropDetails:b.state().imgSelect.getSelection()}),this.$el.text(d.cropping),this.$el.attr("disabled",!0),b.state().doCrop(a).done(function(a){b.trigger("cropped",a),b.close()}).fail(function(){b.trigger("content:error:crop")})}}}},a&&_.extend(b.items,{skip:{style:"secondary",text:d.skipCropping,priority:70,requires:{library:!1,selection:!1},click:function(){var a=this.controller.state().get("selection").first();this.controller.state().cropperView.remove(),this.controller.trigger("skippedcrop",a),this.controller.close()}}}),this.frame.toolbar.set(new wp.media.view.Toolbar(b))},doCrop:function(a){return wp.ajax.post("custom-header-crop",_.extend({},this.defaults.doCropArgs,{nonce:a.get("nonces").edit,id:a.get("id"),cropDetails:a.get("cropDetails")}))}}),a.exports=c},function(a,b){var c,d=wp.media.controller;c=d.Cropper.extend({doCrop:function(a){var b=a.get("cropDetails"),c=this.get("control"),d=b.width/b.height;return c.params.flex_width&&c.params.flex_height?(b.dst_width=b.width,b.dst_height=b.height):(b.dst_width=c.params.flex_width?c.params.height*d:c.params.width,b.dst_height=c.params.flex_height?c.params.width/d:c.params.height),wp.ajax.post("crop-image",{wp_customize:"on",nonce:a.get("nonces").edit,id:a.get("id"),context:c.id,cropDetails:b})}}),a.exports=c},function(a,b){var c,d=wp.media.controller;c=d.Cropper.extend({activate:function(){this.frame.on("content:create:crop",this.createCropContent,this),this.frame.on("close",this.removeCropper,this),this.set("selection",new Backbone.Collection(this.frame._selection.single))},createCropContent:function(){this.cropperView=new wp.media.view.SiteIconCropper({controller:this,attachment:this.get("selection").first()}),this.cropperView.on("image-loaded",this.createCropToolbar,this),this.frame.content.set(this.cropperView)},doCrop:function(a){var b=a.get("cropDetails"),c=this.get("control");return b.dst_width=c.params.width,b.dst_height=c.params.height,wp.ajax.post("crop-image",{nonce:a.get("nonces").edit,id:a.get("id"),context:"site-icon",cropDetails:b})}}),a.exports=c},function(a,b){var c=wp.Backbone.View.extend({constructor:function(a){a&&a.controller&&(this.controller=a.controller),wp.Backbone.View.apply(this,arguments)},dispose:function(){return this.undelegateEvents(),this.model&&this.model.off&&this.model.off(null,null,this),this.collection&&this.collection.off&&this.collection.off(null,null,this),this.controller&&this.controller.off&&this.controller.off(null,null,this),this},remove:function(){return this.dispose(),wp.Backbone.View.prototype.remove.apply(this,arguments)}});a.exports=c},function(a,b){var c=wp.media.View.extend({initialize:function(){_.defaults(this.options,{mode:["select"]}),this._createRegions(),this._createStates(),this._createModes()},_createRegions:function(){this.regions=this.regions?this.regions.slice():[],_.each(this.regions,function(a){this[a]=new wp.media.controller.Region({view:this,id:a,selector:".media-frame-"+a})},this)},_createStates:function(){this.states=new Backbone.Collection(null,{model:wp.media.controller.State}),this.states.on("add",function(a){a.frame=this,a.trigger("ready")},this),this.options.states&&this.states.add(this.options.states)},_createModes:function(){this.activeModes=new Backbone.Collection,this.activeModes.on("add remove reset",_.bind(this.triggerModeEvents,this)),_.each(this.options.mode,function(a){this.activateMode(a)},this)},reset:function(){return this.states.invoke("trigger","reset"),this},triggerModeEvents:function(a,b,c){var d,e,f={add:"activate",remove:"deactivate"};_.each(c,function(a,b){a&&(d=b)}),_.has(f,d)&&(e=a.get("id")+":"+f[d],this.trigger(e))},activateMode:function(a){if(!this.isModeActive(a))return this.activeModes.add([{id:a}]),this.$el.addClass("mode-"+a),this},deactivateMode:function(a){return this.isModeActive(a)?(this.activeModes.remove(this.activeModes.where({id:a})),this.$el.removeClass("mode-"+a),this.trigger(a+":deactivate"),this):this},isModeActive:function(a){return Boolean(this.activeModes.where({id:a}).length)}});_.extend(c.prototype,wp.media.controller.StateMachine.prototype),a.exports=c},function(a,b){var c,d=wp.media.view.Frame,e=jQuery;c=d.extend({className:"media-frame",template:wp.template("media-frame"),regions:["menu","title","content","toolbar","router"],events:{"click div.media-frame-title h1":"toggleMenu"},initialize:function(){d.prototype.initialize.apply(this,arguments),_.defaults(this.options,{title:"",modal:!0,uploader:!0}),this.$el.addClass("wp-core-ui"),this.options.modal&&(this.modal=new wp.media.view.Modal({controller:this,title:this.options.title}),this.modal.content(this)),!wp.Uploader.limitExceeded&&wp.Uploader.browser.supported||(this.options.uploader=!1),this.options.uploader&&(this.uploader=new wp.media.view.UploaderWindow({controller:this,uploader:{dropzone:this.modal?this.modal.$el:this.$el,container:this.$el}}),this.views.set(".media-frame-uploader",this.uploader)),this.on("attach",_.bind(this.views.ready,this.views),this),this.on("title:create:default",this.createTitle,this),this.title.mode("default"),this.on("title:render",function(a){a.$el.append('<span class="dashicons dashicons-arrow-down"></span>')}),this.on("menu:create:default",this.createMenu,this)},render:function(){return!this.state()&&this.options.state&&this.setState(this.options.state),d.prototype.render.apply(this,arguments)},createTitle:function(a){a.view=new wp.media.View({controller:this,tagName:"h1"})},createMenu:function(a){a.view=new wp.media.view.Menu({controller:this})},toggleMenu:function(){this.$el.find(".media-menu").toggleClass("visible")},createToolbar:function(a){a.view=new wp.media.view.Toolbar({controller:this})},createRouter:function(a){a.view=new wp.media.view.Router({controller:this})},createIframeStates:function(a){var b,c=wp.media.view.settings,d=c.tabs,f=c.tabUrl;d&&f&&(b=e("#post_ID"),b.length&&(f+="&post_id="+b.val()),_.each(d,function(b,c){this.state("iframe:"+c).set(_.defaults({tab:c,src:f+"&tab="+c,title:b,content:"iframe",menu:"default"},a))},this),this.on("content:create:iframe",this.iframeContent,this),this.on("content:deactivate:iframe",this.iframeContentCleanup,this),this.on("menu:render:default",this.iframeMenu,this),this.on("open",this.hijackThickbox,this),this.on("close",this.restoreThickbox,this))},iframeContent:function(a){this.$el.addClass("hide-toolbar"),a.view=new wp.media.view.Iframe({controller:this})},iframeContentCleanup:function(){this.$el.removeClass("hide-toolbar")},iframeMenu:function(a){var b={};a&&(_.each(wp.media.view.settings.tabs,function(a,c){b["iframe:"+c]={text:this.state("iframe:"+c).get("title"),priority:200}},this),a.set(b))},hijackThickbox:function(){var a=this;window.tb_remove&&!this._tb_remove&&(this._tb_remove=window.tb_remove,window.tb_remove=function(){a.close(),a.reset(),a.setState(a.options.state),a._tb_remove.call(window)})},restoreThickbox:function(){this._tb_remove&&(window.tb_remove=this._tb_remove,delete this._tb_remove)}}),_.each(["open","close","attach","detach","escape"],function(a){c.prototype[a]=function(){return this.modal&&this.modal[a].apply(this.modal,arguments),this}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame,e=wp.media.view.l10n;c=d.extend({initialize:function(){d.prototype.initialize.apply(this,arguments),_.defaults(this.options,{selection:[],library:{},multiple:!1,state:"library"}),this.createSelection(),this.createStates(),this.bindHandlers()},createSelection:function(){var a=this.options.selection;a instanceof wp.media.model.Selection||(this.options.selection=new wp.media.model.Selection(a,{multiple:this.options.multiple})),this._selection={attachments:new wp.media.model.Attachments,difference:[]}},createStates:function(){var a=this.options;this.options.states||this.states.add([new wp.media.controller.Library({library:wp.media.query(a.library),multiple:a.multiple,title:a.title,priority:20})])},bindHandlers:function(){this.on("router:create:browse",this.createRouter,this),this.on("router:render:browse",this.browseRouter,this),this.on("content:create:browse",this.browseContent,this),this.on("content:render:upload",this.uploadContent,this),this.on("toolbar:create:select",this.createSelectToolbar,this)},browseRouter:function(a){a.set({upload:{text:e.uploadFilesTitle,priority:20},browse:{text:e.mediaLibraryTitle,priority:40}})},browseContent:function(a){var b=this.state();this.$el.removeClass("hide-toolbar"),a.view=new wp.media.view.AttachmentsBrowser({controller:this,collection:b.get("library"),selection:b.get("selection"),model:b,sortable:b.get("sortable"),search:b.get("searchable"),filters:b.get("filterable"),date:b.get("date"),display:b.has("display")?b.get("display"):b.get("displaySettings"),dragInfo:b.get("dragInfo"),idealColumnWidth:b.get("idealColumnWidth"),suggestedWidth:b.get("suggestedWidth"),suggestedHeight:b.get("suggestedHeight"),AttachmentView:b.get("AttachmentView")})},uploadContent:function(){this.$el.removeClass("hide-toolbar"),this.content.set(new wp.media.view.UploaderInline({controller:this}))},createSelectToolbar:function(a,b){b=b||this.options.button||{},b.controller=this,a.view=new wp.media.view.Toolbar.Select(b)}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame.Select,e=wp.media.controller.Library,f=wp.media.view.l10n;c=d.extend({initialize:function(){this.counts={audio:{count:wp.media.view.settings.attachmentCounts.audio,state:"playlist"},video:{count:wp.media.view.settings.attachmentCounts.video,state:"video-playlist"}},_.defaults(this.options,{multiple:!0,editing:!1,state:"insert",metadata:{}}),d.prototype.initialize.apply(this,arguments),this.createIframeStates()},createStates:function(){var a=this.options;this.states.add([new e({id:"insert",title:f.insertMediaTitle,priority:20,toolbar:"main-insert",filterable:"all",library:wp.media.query(a.library),multiple:!!a.multiple&&"reset",editable:!0,allowLocalEdits:!0,displaySettings:!0,displayUserSettings:!0}),new e({id:"gallery",title:f.createGalleryTitle,priority:40,toolbar:"main-gallery",filterable:"uploaded",multiple:"add",editable:!1,library:wp.media.query(_.defaults({type:"image"},a.library))}),new wp.media.controller.Embed({metadata:a.metadata}),new wp.media.controller.EditImage({model:a.editImage}),new wp.media.controller.GalleryEdit({library:a.selection,editing:a.editing,menu:"gallery"}),new wp.media.controller.GalleryAdd,new e({id:"playlist",title:f.createPlaylistTitle,priority:60,toolbar:"main-playlist",filterable:"uploaded",multiple:"add",editable:!1,library:wp.media.query(_.defaults({type:"audio"},a.library))}),new wp.media.controller.CollectionEdit({type:"audio",collectionType:"playlist",title:f.editPlaylistTitle,SettingsView:wp.media.view.Settings.Playlist,library:a.selection,editing:a.editing,menu:"playlist",dragInfoText:f.playlistDragInfo,dragInfo:!1}),new wp.media.controller.CollectionAdd({type:"audio",collectionType:"playlist",title:f.addToPlaylistTitle}),new e({id:"video-playlist",title:f.createVideoPlaylistTitle,priority:60,toolbar:"main-video-playlist",filterable:"uploaded",multiple:"add",editable:!1,library:wp.media.query(_.defaults({
            type:"video"},a.library))}),new wp.media.controller.CollectionEdit({type:"video",collectionType:"playlist",title:f.editVideoPlaylistTitle,SettingsView:wp.media.view.Settings.Playlist,library:a.selection,editing:a.editing,menu:"video-playlist",dragInfoText:f.videoPlaylistDragInfo,dragInfo:!1}),new wp.media.controller.CollectionAdd({type:"video",collectionType:"playlist",title:f.addToVideoPlaylistTitle})]),wp.media.view.settings.post.featuredImageId&&this.states.add(new wp.media.controller.FeaturedImage)},bindHandlers:function(){var a,b;d.prototype.bindHandlers.apply(this,arguments),this.on("activate",this.activate,this),b=_.find(this.counts,function(a){return 0===a.count}),"undefined"!=typeof b&&this.listenTo(wp.media.model.Attachments.all,"change:type",this.mediaTypeCounts),this.on("menu:create:gallery",this.createMenu,this),this.on("menu:create:playlist",this.createMenu,this),this.on("menu:create:video-playlist",this.createMenu,this),this.on("toolbar:create:main-insert",this.createToolbar,this),this.on("toolbar:create:main-gallery",this.createToolbar,this),this.on("toolbar:create:main-playlist",this.createToolbar,this),this.on("toolbar:create:main-video-playlist",this.createToolbar,this),this.on("toolbar:create:featured-image",this.featuredImageToolbar,this),this.on("toolbar:create:main-embed",this.mainEmbedToolbar,this),a={menu:{"default":"mainMenu",gallery:"galleryMenu",playlist:"playlistMenu","video-playlist":"videoPlaylistMenu"},content:{embed:"embedContent","edit-image":"editImageContent","edit-selection":"editSelectionContent"},toolbar:{"main-insert":"mainInsertToolbar","main-gallery":"mainGalleryToolbar","gallery-edit":"galleryEditToolbar","gallery-add":"galleryAddToolbar","main-playlist":"mainPlaylistToolbar","playlist-edit":"playlistEditToolbar","playlist-add":"playlistAddToolbar","main-video-playlist":"mainVideoPlaylistToolbar","video-playlist-edit":"videoPlaylistEditToolbar","video-playlist-add":"videoPlaylistAddToolbar"}},_.each(a,function(a,b){_.each(a,function(a,c){this.on(b+":render:"+c,this[a],this)},this)},this)},activate:function(){_.each(this.counts,function(a){a.count<1&&this.menuItemVisibility(a.state,"hide")},this)},mediaTypeCounts:function(a,b){"undefined"!=typeof this.counts[b]&&this.counts[b].count<1&&(this.counts[b].count++,this.menuItemVisibility(this.counts[b].state,"show"))},mainMenu:function(a){a.set({"library-separator":new wp.media.View({className:"separator",priority:100})})},menuItemVisibility:function(a,b){var c=this.menu.get();"hide"===b?c.hide(a):"show"===b&&c.show(a)},galleryMenu:function(a){var b=this.lastState(),c=b&&b.id,d=this;a.set({cancel:{text:f.cancelGalleryTitle,priority:20,click:function(){c?d.setState(c):d.close(),this.controller.modal.focusManager.focus()}},separateCancel:new wp.media.View({className:"separator",priority:40})})},playlistMenu:function(a){var b=this.lastState(),c=b&&b.id,d=this;a.set({cancel:{text:f.cancelPlaylistTitle,priority:20,click:function(){c?d.setState(c):d.close()}},separateCancel:new wp.media.View({className:"separator",priority:40})})},videoPlaylistMenu:function(a){var b=this.lastState(),c=b&&b.id,d=this;a.set({cancel:{text:f.cancelVideoPlaylistTitle,priority:20,click:function(){c?d.setState(c):d.close()}},separateCancel:new wp.media.View({className:"separator",priority:40})})},embedContent:function(){var a=new wp.media.view.Embed({controller:this,model:this.state()}).render();this.content.set(a),wp.media.isTouchDevice||a.url.focus()},editSelectionContent:function(){var a,b=this.state(),c=b.get("selection");a=new wp.media.view.AttachmentsBrowser({controller:this,collection:c,selection:c,model:b,sortable:!0,search:!1,date:!1,dragInfo:!0,AttachmentView:wp.media.view.Attachments.EditSelection}).render(),a.toolbar.set("backToLibrary",{text:f.returnToLibrary,priority:-100,click:function(){this.controller.content.mode("browse")}}),this.content.set(a),this.trigger("edit:selection",this)},editImageContent:function(){var a=this.state().get("image"),b=new wp.media.view.EditImage({model:a,controller:this}).render();this.content.set(b),b.loadEditor()},selectionStatusToolbar:function(a){var b=this.state().get("editable");a.set("selection",new wp.media.view.Selection({controller:this,collection:this.state().get("selection"),priority:-40,editable:b&&function(){this.controller.content.mode("edit-selection")}}).render())},mainInsertToolbar:function(a){var b=this;this.selectionStatusToolbar(a),a.set("insert",{style:"primary",priority:80,text:f.insertIntoPost,requires:{selection:!0},click:function(){var a=b.state(),c=a.get("selection");b.close(),a.trigger("insert",c).reset()}})},mainGalleryToolbar:function(a){var b=this;this.selectionStatusToolbar(a),a.set("gallery",{style:"primary",text:f.createNewGallery,priority:60,requires:{selection:!0},click:function(){var a=b.state().get("selection"),c=b.state("gallery-edit"),d=a.where({type:"image"});c.set("library",new wp.media.model.Selection(d,{props:a.props.toJSON(),multiple:!0})),this.controller.setState("gallery-edit"),this.controller.modal.focusManager.focus()}})},mainPlaylistToolbar:function(a){var b=this;this.selectionStatusToolbar(a),a.set("playlist",{style:"primary",text:f.createNewPlaylist,priority:100,requires:{selection:!0},click:function(){var a=b.state().get("selection"),c=b.state("playlist-edit"),d=a.where({type:"audio"});c.set("library",new wp.media.model.Selection(d,{props:a.props.toJSON(),multiple:!0})),this.controller.setState("playlist-edit"),this.controller.modal.focusManager.focus()}})},mainVideoPlaylistToolbar:function(a){var b=this;this.selectionStatusToolbar(a),a.set("video-playlist",{style:"primary",text:f.createNewVideoPlaylist,priority:100,requires:{selection:!0},click:function(){var a=b.state().get("selection"),c=b.state("video-playlist-edit"),d=a.where({type:"video"});c.set("library",new wp.media.model.Selection(d,{props:a.props.toJSON(),multiple:!0})),this.controller.setState("video-playlist-edit"),this.controller.modal.focusManager.focus()}})},featuredImageToolbar:function(a){this.createSelectToolbar(a,{text:f.setFeaturedImage,state:this.options.state})},mainEmbedToolbar:function(a){a.view=new wp.media.view.Toolbar.Embed({controller:this})},galleryEditToolbar:function(){var a=this.state().get("editing");this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:a?f.updateGallery:f.insertGallery,priority:80,requires:{library:!0},click:function(){var a=this.controller,b=a.state();a.close(),b.trigger("update",b.get("library")),a.setState(a.options.state),a.reset()}}}}))},galleryAddToolbar:function(){this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:f.addToGallery,priority:80,requires:{selection:!0},click:function(){var a=this.controller,b=a.state(),c=a.state("gallery-edit");c.get("library").add(b.get("selection").models),b.trigger("reset"),a.setState("gallery-edit")}}}}))},playlistEditToolbar:function(){var a=this.state().get("editing");this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:a?f.updatePlaylist:f.insertPlaylist,priority:80,requires:{library:!0},click:function(){var a=this.controller,b=a.state();a.close(),b.trigger("update",b.get("library")),a.setState(a.options.state),a.reset()}}}}))},playlistAddToolbar:function(){this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:f.addToPlaylist,priority:80,requires:{selection:!0},click:function(){var a=this.controller,b=a.state(),c=a.state("playlist-edit");c.get("library").add(b.get("selection").models),b.trigger("reset"),a.setState("playlist-edit")}}}}))},videoPlaylistEditToolbar:function(){var a=this.state().get("editing");this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:a?f.updateVideoPlaylist:f.insertVideoPlaylist,priority:140,requires:{library:!0},click:function(){var a=this.controller,b=a.state(),c=b.get("library");c.type="video",a.close(),b.trigger("update",c),a.setState(a.options.state),a.reset()}}}}))},videoPlaylistAddToolbar:function(){this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{insert:{style:"primary",text:f.addToVideoPlaylist,priority:140,requires:{selection:!0},click:function(){var a=this.controller,b=a.state(),c=a.state("video-playlist-edit");c.get("library").add(b.get("selection").models),b.trigger("reset"),a.setState("video-playlist-edit")}}}}))}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame.Select,e=wp.media.view.l10n;c=d.extend({defaults:{id:"image",url:"",menu:"image-details",content:"image-details",toolbar:"image-details",type:"link",title:e.imageDetailsTitle,priority:120},initialize:function(a){this.image=new wp.media.model.PostImage(a.metadata),this.options.selection=new wp.media.model.Selection(this.image.attachment,{multiple:!1}),d.prototype.initialize.apply(this,arguments)},bindHandlers:function(){d.prototype.bindHandlers.apply(this,arguments),this.on("menu:create:image-details",this.createMenu,this),this.on("content:create:image-details",this.imageDetailsContent,this),this.on("content:render:edit-image",this.editImageContent,this),this.on("toolbar:render:image-details",this.renderImageDetailsToolbar,this),this.on("toolbar:render:replace",this.renderReplaceImageToolbar,this)},createStates:function(){this.states.add([new wp.media.controller.ImageDetails({image:this.image,editable:!1}),new wp.media.controller.ReplaceImage({id:"replace-image",library:wp.media.query({type:"image"}),image:this.image,multiple:!1,title:e.imageReplaceTitle,toolbar:"replace",priority:80,displaySettings:!0}),new wp.media.controller.EditImage({image:this.image,selection:this.options.selection})])},imageDetailsContent:function(a){a.view=new wp.media.view.ImageDetails({controller:this,model:this.state().image,attachment:this.state().image.attachment})},editImageContent:function(){var a,b=this.state(),c=b.get("image");c&&(a=new wp.media.view.EditImage({model:c,controller:this}).render(),this.content.set(a),a.loadEditor())},renderImageDetailsToolbar:function(){this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{select:{style:"primary",text:e.update,priority:80,click:function(){var a=this.controller,b=a.state();a.close(),b.trigger("update",a.image.toJSON()),a.setState(a.options.state),a.reset()}}}}))},renderReplaceImageToolbar:function(){var a=this,b=a.lastState(),c=b&&b.id;this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{back:{text:e.back,priority:20,click:function(){c?a.setState(c):a.close()}},replace:{style:"primary",text:e.replace,priority:80,requires:{selection:!0},click:function(){var a=this.controller,b=a.state(),c=b.get("selection"),d=c.single();a.close(),a.image.changeAttachment(d,b.display(d)),b.trigger("replace",a.image.toJSON()),a.setState(a.options.state),a.reset()}}}}))}}),a.exports=c},function(a,b){var c,d=jQuery;c=wp.media.View.extend({tagName:"div",template:wp.template("media-modal"),events:{"click .media-modal-backdrop, .media-modal-close":"escapeHandler",keydown:"keydown"},clickedOpenerEl:null,initialize:function(){_.defaults(this.options,{container:document.body,title:"",propagate:!0}),this.focusManager=new wp.media.view.FocusManager({el:this.el})},prepare:function(){return{title:this.options.title}},attach:function(){return this.views.attached?this:(this.views.rendered||this.render(),this.$el.appendTo(this.options.container),this.views.attached=!0,this.views.ready(),this.propagate("attach"))},detach:function(){return this.$el.is(":visible")&&this.close(),this.$el.detach(),this.views.attached=!1,this.propagate("detach")},open:function(){var a,b=this.$el;return b.is(":visible")?this:(this.clickedOpenerEl=document.activeElement,this.views.attached||this.attach(),d("body").addClass("modal-open"),b.show(),"ontouchend"in document&&(a=window.tinymce&&window.tinymce.activeEditor)&&!a.isHidden()&&a.iframeElement&&(a.iframeElement.focus(),a.iframeElement.blur(),setTimeout(function(){a.iframeElement.blur()},100)),this.$(".media-modal").focus(),this.propagate("open"))},close:function(a){return this.views.attached&&this.$el.is(":visible")?(d("body").removeClass("modal-open"),this.$el.hide().undelegate("keydown"),null!==this.clickedOpenerEl?this.clickedOpenerEl.focus():d("#wpbody-content").focus(),this.propagate("close"),a&&a.escape&&this.propagate("escape"),this):this},escape:function(){return this.close({escape:!0})},escapeHandler:function(a){a.preventDefault(),this.escape()},content:function(a){return this.views.set(".media-modal-content",a),this},propagate:function(a){return this.trigger(a),this.options.propagate&&this.controller.trigger(a),this},keydown:function(a){27===a.which&&this.$el.is(":visible")&&(this.escape(),a.stopImmediatePropagation())}}),a.exports=c},function(a,b){var c=wp.media.View.extend({events:{keydown:"constrainTabbing"},focus:function(){this.$(".media-menu-item").first().focus()},constrainTabbing:function(a){var b;if(9===a.keyCode)return b=this.$(":tabbable").not('.moxie-shim input[type="file"]'),b.last()[0]!==a.target||a.shiftKey?b.first()[0]===a.target&&a.shiftKey?(b.last().focus(),!1):void 0:(b.first().focus(),!1)}});a.exports=c},function(a,b){var c,d=jQuery;c=wp.media.View.extend({tagName:"div",className:"uploader-window",template:wp.template("uploader-window"),initialize:function(){var a;this.$browser=d('<button type="button" class="browser" />').hide().appendTo("body"),a=this.options.uploader=_.defaults(this.options.uploader||{},{dropzone:this.$el,browser:this.$browser,params:{}}),!a.dropzone||a.dropzone instanceof d||(a.dropzone=d(a.dropzone)),this.controller.on("activate",this.refresh,this),this.controller.on("detach",function(){this.$browser.remove()},this)},refresh:function(){this.uploader&&this.uploader.refresh()},ready:function(){var a,b=wp.media.view.settings.post.id;this.uploader||(b&&(this.options.uploader.params.post_id=b),this.uploader=new wp.Uploader(this.options.uploader),a=this.uploader.dropzone,a.on("dropzone:enter",_.bind(this.show,this)),a.on("dropzone:leave",_.bind(this.hide,this)),d(this.uploader).on("uploader:ready",_.bind(this._ready,this)))},_ready:function(){this.controller.trigger("uploader:ready")},show:function(){var a=this.$el.show();_.defer(function(){a.css({opacity:1})})},hide:function(){var a=this.$el.css({opacity:0});wp.media.transition(a).done(function(){"0"===a.css("opacity")&&a.hide()}),_.delay(function(){"0"===a.css("opacity")&&a.is(":visible")&&a.hide()},500)}}),a.exports=c},function(a,b){var c,d=wp.media.View,e=wp.media.view.l10n,f=jQuery;c=d.extend({tagName:"div",className:"uploader-editor",template:wp.template("uploader-editor"),localDrag:!1,overContainer:!1,overDropzone:!1,draggingFile:null,initialize:function(){return this.initialized=!1,window.tinyMCEPreInit&&window.tinyMCEPreInit.dragDropUpload&&this.browserSupport()?(this.$document=f(document),this.dropzones=[],this.files=[],this.$document.on("drop",".uploader-editor",_.bind(this.drop,this)),this.$document.on("dragover",".uploader-editor",_.bind(this.dropzoneDragover,this)),this.$document.on("dragleave",".uploader-editor",_.bind(this.dropzoneDragleave,this)),this.$document.on("click",".uploader-editor",_.bind(this.click,this)),this.$document.on("dragover",_.bind(this.containerDragover,this)),this.$document.on("dragleave",_.bind(this.containerDragleave,this)),this.$document.on("dragstart dragend drop",_.bind(function(a){this.localDrag="dragstart"===a.type,"drop"===a.type&&this.containerDragleave()},this)),this.initialized=!0,this):this},browserSupport:function(){var a=!1,b=document.createElement("div");return a="draggable"in b||"ondragstart"in b&&"ondrop"in b,a=a&&!!(window.File&&window.FileList&&window.FileReader)},isDraggingFile:function(a){return null!==this.draggingFile?this.draggingFile:!_.isUndefined(a.originalEvent)&&!_.isUndefined(a.originalEvent.dataTransfer)&&(this.draggingFile=_.indexOf(a.originalEvent.dataTransfer.types,"Files")>-1&&_.indexOf(a.originalEvent.dataTransfer.types,"text/plain")===-1,this.draggingFile)},refresh:function(a){var b;for(b in this.dropzones)this.dropzones[b].toggle(this.overContainer||this.overDropzone);return _.isUndefined(a)||f(a.target).closest(".uploader-editor").toggleClass("droppable",this.overDropzone),this.overContainer||this.overDropzone||(this.draggingFile=null),this},render:function(){return this.initialized?(d.prototype.render.apply(this,arguments),f(".wp-editor-wrap").each(_.bind(this.attach,this)),this):this},attach:function(a,b){var c=this.$el.clone();return this.dropzones.push(c),f(b).append(c),this},drop:function(a){var b,c;if(this.containerDragleave(a),this.dropzoneDragleave(a),this.files=a.originalEvent.dataTransfer.files,!(this.files.length<1))return b=f(a.target).parents(".wp-editor-wrap"),b.length>0&&b[0].id&&(window.wpActiveEditor=b[0].id.slice(3,-5)),this.workflow?(this.workflow.state().reset(),this.addFiles.apply(this),this.workflow.open()):(this.workflow=wp.media.editor.open(window.wpActiveEditor,{frame:"post",state:"insert",title:e.addMedia,multiple:!0}),c=this.workflow.uploader,c.uploader&&c.uploader.ready?this.addFiles.apply(this):this.workflow.on("uploader:ready",this.addFiles,this)),!1},addFiles:function(){return this.files.length&&(this.workflow.uploader.uploader.uploader.addFile(_.toArray(this.files)),this.files=[]),this},containerDragover:function(a){!this.localDrag&&this.isDraggingFile(a)&&(this.overContainer=!0,this.refresh())},containerDragleave:function(){this.overContainer=!1,_.delay(_.bind(this.refresh,this),50)},dropzoneDragover:function(a){if(!this.localDrag&&this.isDraggingFile(a))return this.overDropzone=!0,this.refresh(a),!1},dropzoneDragleave:function(a){this.overDropzone=!1,_.delay(_.bind(this.refresh,this,a),50)},click:function(a){this.containerDragleave(a),this.dropzoneDragleave(a),this.localDrag=!1}}),a.exports=c},function(a,b){var c,d=wp.media.View;c=d.extend({tagName:"div",className:"uploader-inline",template:wp.template("uploader-inline"),events:{"click .close":"hide"},initialize:function(){_.defaults(this.options,{message:"",status:!0,canClose:!1}),!this.options.$browser&&this.controller.uploader&&(this.options.$browser=this.controller.uploader.$browser),_.isUndefined(this.options.postId)&&(this.options.postId=wp.media.view.settings.post.id),this.options.status&&this.views.set(".upload-inline-status",new wp.media.view.UploaderStatus({controller:this.controller}))},prepare:function(){var a=this.controller.state().get("suggestedWidth"),b=this.controller.state().get("suggestedHeight"),c={};return c.message=this.options.message,c.canClose=this.options.canClose,a&&b&&(c.suggestedWidth=a,c.suggestedHeight=b),c},dispose:function(){return this.disposing?d.prototype.dispose.apply(this,arguments):(this.disposing=!0,this.remove())},remove:function(){var a=d.prototype.remove.apply(this,arguments);return _.defer(_.bind(this.refresh,this)),a},refresh:function(){var a=this.controller.uploader;a&&a.refresh()},ready:function(){var a,b=this.options.$browser;if(this.controller.uploader){if(a=this.$(".browser"),a[0]===b[0])return;b.detach().text(a.text()),b[0].className=a[0].className,a.replaceWith(b.show())}return this.refresh(),this},show:function(){this.$el.removeClass("hidden"),this.controller.$uploaderToggler&&this.controller.$uploaderToggler.length&&this.controller.$uploaderToggler.attr("aria-expanded","true")},hide:function(){this.$el.addClass("hidden"),this.controller.$uploaderToggler&&this.controller.$uploaderToggler.length&&this.controller.$uploaderToggler.attr("aria-expanded","false").focus()}}),a.exports=c},function(a,b){var c,d=wp.media.View;c=d.extend({className:"media-uploader-status",template:wp.template("uploader-status"),events:{"click .upload-dismiss-errors":"dismiss"},initialize:function(){this.queue=wp.Uploader.queue,this.queue.on("add remove reset",this.visibility,this),this.queue.on("add remove reset change:percent",this.progress,this),this.queue.on("add remove reset change:uploading",this.info,this),this.errors=wp.Uploader.errors,this.errors.reset(),this.errors.on("add remove reset",this.visibility,this),this.errors.on("add",this.error,this)},dispose:function(){return wp.Uploader.queue.off(null,null,this),d.prototype.dispose.apply(this,arguments),this},visibility:function(){this.$el.toggleClass("uploading",!!this.queue.length),this.$el.toggleClass("errors",!!this.errors.length),this.$el.toggle(!!this.queue.length||!!this.errors.length)},ready:function(){_.each({$bar:".media-progress-bar div",$index:".upload-index",$total:".upload-total",$filename:".upload-filename"},function(a,b){this[b]=this.$(a)},this),this.visibility(),this.progress(),this.info()},progress:function(){var a=this.queue,b=this.$bar;b&&a.length&&b.width(a.reduce(function(a,b){if(!b.get("uploading"))return a+100;var c=b.get("percent");return a+(_.isNumber(c)?c:100)},0)/a.length+"%")},info:function(){var a,b=this.queue,c=0;b.length&&(a=this.queue.find(function(a,b){return c=b,a.get("uploading")}),this.$index.text(c+1),this.$total.text(b.length),this.$filename.html(a?this.filename(a.get("filename")):""))},filename:function(a){return _.escape(a)},error:function(a){this.views.add(".upload-errors",new wp.media.view.UploaderStatusError({filename:this.filename(a.get("file").name),message:a.get("message")}),{at:0})},dismiss:function(a){var b=this.views.get(".upload-errors");a.preventDefault(),b&&_.invoke(b,"remove"),wp.Uploader.errors.reset()}}),a.exports=c},function(a,b){var c=wp.media.View.extend({className:"upload-error",template:wp.template("uploader-status-error")});a.exports=c},function(a,b){var c,d=wp.media.View;c=d.extend({tagName:"div",className:"media-toolbar",initialize:function(){var a=this.controller.state(),b=this.selection=a.get("selection"),c=this.library=a.get("library");this._views={},this.primary=new wp.media.view.PriorityList,this.secondary=new wp.media.view.PriorityList,this.primary.$el.addClass("media-toolbar-primary search-form"),this.secondary.$el.addClass("media-toolbar-secondary"),this.views.set([this.secondary,this.primary]),this.options.items&&this.set(this.options.items,{silent:!0}),this.options.silent||this.render(),b&&b.on("add remove reset",this.refresh,this),c&&c.on("add remove reset",this.refresh,this)},dispose:function(){return this.selection&&this.selection.off(null,null,this),this.library&&this.library.off(null,null,this),d.prototype.dispose.apply(this,arguments)},ready:function(){this.refresh()},set:function(a,b,c){var d;return c=c||{},_.isObject(a)?_.each(a,function(a,b){this.set(b,a,{silent:!0})},this):(b instanceof Backbone.View||(b.classes=["media-button-"+a].concat(b.classes||[]),b=new wp.media.view.Button(b).render()),b.controller=b.controller||this.controller,this._views[a]=b,d=b.options.priority<0?"secondary":"primary",this[d].set(a,b,c)),c.silent||this.refresh(),this},get:function(a){return this._views[a]},unset:function(a,b){return delete this._views[a],this.primary.unset(a,b),this.secondary.unset(a,b),b&&b.silent||this.refresh(),this},refresh:function(){var a=this.controller.state(),b=a.get("library"),c=a.get("selection");_.each(this._views,function(a){if(a.model&&a.options&&a.options.requires){var d=a.options.requires,e=!1;c&&c.models&&(e=_.some(c.models,function(a){return a.get("uploading")===!0})),d.selection&&c&&!c.length?e=!0:d.library&&b&&!b.length&&(e=!0),a.model.set("disabled",e)}})}}),a.exports=c},function(a,b){var c,d=wp.media.view.Toolbar,e=wp.media.view.l10n;c=d.extend({initialize:function(){var a=this.options;_.bindAll(this,"clickSelect"),_.defaults(a,{event:"select",state:!1,reset:!0,close:!0,text:e.select,requires:{selection:!0}}),a.items=_.defaults(a.items||{},{select:{style:"primary",text:a.text,priority:80,click:this.clickSelect,requires:a.requires}}),d.prototype.initialize.apply(this,arguments)},clickSelect:function(){var a=this.options,b=this.controller;a.close&&b.close(),a.event&&b.state().trigger(a.event),a.state&&b.setState(a.state),a.reset&&b.reset()}}),a.exports=c},function(a,b){var c,d=wp.media.view.Toolbar.Select,e=wp.media.view.l10n;c=d.extend({initialize:function(){_.defaults(this.options,{text:e.insertIntoPost,requires:!1}),d.prototype.initialize.apply(this,arguments)},refresh:function(){var a=this.controller.state().props.get("url");this.get("select").model.set("disabled",!a||"http://"===a),d.prototype.refresh.apply(this,arguments)}}),a.exports=c},function(a,b){var c=wp.media.View.extend({tagName:"button",className:"media-button",attributes:{type:"button"},events:{click:"click"},defaults:{text:"",style:"",size:"large",disabled:!1},initialize:function(){this.model=new Backbone.Model(this.defaults),_.each(this.defaults,function(a,b){var c=this.options[b];_.isUndefined(c)||(this.model.set(b,c),delete this.options[b])},this),this.listenTo(this.model,"change",this.render)},render:function(){var a=["button",this.className],b=this.model.toJSON();return b.style&&a.push("button-"+b.style),b.size&&a.push("button-"+b.size),a=_.uniq(a.concat(this.options.classes)),this.el.className=a.join(" "),this.$el.attr("disabled",b.disabled),this.$el.text(this.model.get("text")),this},click:function(a){"#"===this.attributes.href&&a.preventDefault(),this.options.click&&!this.model.get("disabled")&&this.options.click.apply(this,arguments)}});a.exports=c},function(a,b){var c,d=Backbone.$;c=wp.media.View.extend({tagName:"div",className:"button-group button-large media-button-group",initialize:function(){this.buttons=_.map(this.options.buttons||[],function(a){return a instanceof Backbone.View?a:new wp.media.view.Button(a).render()}),delete this.options.buttons,this.options.classes&&this.$el.addClass(this.options.classes)},render:function(){return this.$el.html(d(_.pluck(this.buttons,"el")).detach()),this}}),a.exports=c},function(a,b){var c=wp.media.View.extend({tagName:"div",initialize:function(){this._views={},this.set(_.extend({},this._views,this.options.views),{silent:!0}),delete this.options.views,this.options.silent||this.render()},set:function(a,b,c){var d,e,f;return c=c||{},_.isObject(a)?(_.each(a,function(a,b){this.set(b,a)},this),this):(b instanceof Backbone.View||(b=this.toView(b,a,c)),b.controller=b.controller||this.controller,this.unset(a),d=b.options.priority||10,e=this.views.get()||[],_.find(e,function(a,b){if(a.options.priority>d)return f=b,!0}),this._views[a]=b,this.views.add(b,{at:_.isNumber(f)?f:e.length||0}),this)},get:function(a){return this._views[a]},unset:function(a){var b=this.get(a);return b&&b.remove(),delete this._views[a],this},toView:function(a){return new wp.media.View(a)}});a.exports=c},function(a,b){var c,d=jQuery;c=wp.media.View.extend({tagName:"a",className:"media-menu-item",attributes:{href:"#"},events:{click:"_click"},_click:function(a){var b=this.options.click;a&&a.preventDefault(),b?b.call(this):this.click(),wp.media.isTouchDevice||d(".media-frame-content input").first().focus()},click:function(){var a=this.options.state;a&&(this.controller.setState(a),this.views.parent.$el.removeClass("visible"))},render:function(){var a=this.options;return a.text?this.$el.text(a.text):a.html&&this.$el.html(a.html),this}}),a.exports=c},function(a,b){var c,d=wp.media.view.MenuItem,e=wp.media.view.PriorityList;c=e.extend({tagName:"div",className:"media-menu",property:"state",ItemView:d,region:"menu",toView:function(a,b){return a=a||{},a[this.property]=a[this.property]||b,new this.ItemView(a).render()},ready:function(){e.prototype.ready.apply(this,arguments),this.visibility()},set:function(){e.prototype.set.apply(this,arguments),this.visibility()},unset:function(){e.prototype.unset.apply(this,arguments),this.visibility()},visibility:function(){var a=this.region,b=this.controller[a].get(),c=this.views.get(),d=!c||c.length<2;this===b&&this.controller.$el.toggleClass("hide-"+a,d)},select:function(a){var b=this.get(a);b&&(this.deselect(),b.$el.addClass("active"))},deselect:function(){this.$el.children().removeClass("active")},hide:function(a){var b=this.get(a);b&&b.$el.addClass("hidden")},show:function(a){var b=this.get(a);b&&b.$el.removeClass("hidden")}}),a.exports=c},function(a,b){var c=wp.media.view.MenuItem.extend({click:function(){var a=this.options.contentMode;a&&this.controller.content.mode(a)}});a.exports=c},function(a,b){var c,d=wp.media.view.Menu;c=d.extend({tagName:"div",className:"media-router",property:"contentMode",ItemView:wp.media.view.RouterItem,region:"router",initialize:function(){this.controller.on("content:render",this.update,this),d.prototype.initialize.apply(this,arguments)},update:function(){var a=this.controller.content.mode();a&&this.select(a)}}),a.exports=c},function(a,b){var c=wp.media.view.PriorityList.extend({className:"media-sidebar"});a.exports=c},function(a,b){var c,d=wp.media.View,e=jQuery;c=d.extend({tagName:"li",className:"attachment",template:wp.template("attachment"),attributes:function(){return{tabIndex:0,role:"checkbox","aria-label":this.model.get("title"),"aria-checked":!1,"data-id":this.model.get("id")}},events:{click:"toggleSelectionHandler","change [data-setting]":"updateSetting","change [data-setting] input":"updateSetting","change [data-setting] select":"updateSetting","change [data-setting] textarea":"updateSetting","click .attachment-close":"removeFromLibrary","click .check":"checkClickHandler",keydown:"toggleSelectionHandler"},buttons:{},initialize:function(){var a=this.options.selection,b=_.defaults(this.options,{rerenderOnModelChange:!0});b.rerenderOnModelChange?this.listenTo(this.model,"change",this.render):this.listenTo(this.model,"change:percent",this.progress),this.listenTo(this.model,"change:title",this._syncTitle),this.listenTo(this.model,"change:caption",this._syncCaption),this.listenTo(this.model,"change:artist",this._syncArtist),this.listenTo(this.model,"change:album",this._syncAlbum),this.listenTo(this.model,"add",this.select),this.listenTo(this.model,"remove",this.deselect),a&&(a.on("reset",this.updateSelect,this),this.listenTo(this.model,"selection:single selection:unsingle",this.details),this.details(this.model,this.controller.state().get("selection"))),this.listenTo(this.controller,"attachment:compat:waiting attachment:compat:ready",this.updateSave)},dispose:function(){var a=this.options.selection;return this.updateAll(),a&&a.off(null,null,this),d.prototype.dispose.apply(this,arguments),this},render:function(){var a=_.defaults(this.model.toJSON(),{orientation:"landscape",uploading:!1,type:"",subtype:"",icon:"",filename:"",caption:"",title:"",dateFormatted:"",width:"",height:"",compat:!1,alt:"",description:""},this.options);return a.buttons=this.buttons,a.describe=this.controller.state().get("describe"),"image"===a.type&&(a.size=this.imageSize()),a.can={},a.nonces&&(a.can.remove=!!a.nonces["delete"],a.can.save=!!a.nonces.update),this.controller.state().get("allowLocalEdits")&&(a.allowLocalEdits=!0),a.uploading&&!a.percent&&(a.percent=0),this.views.detach(),this.$el.html(this.template(a)),this.$el.toggleClass("uploading",a.uploading),a.uploading?this.$bar=this.$(".media-progress-bar div"):delete this.$bar,this.updateSelect(),this.updateSave(),this.views.render(),this},progress:function(){this.$bar&&this.$bar.length&&this.$bar.width(this.model.get("percent")+"%")},toggleSelectionHandler:function(a){var b;if("INPUT"!==a.target.nodeName&&"BUTTON"!==a.target.nodeName){if(37===a.keyCode||38===a.keyCode||39===a.keyCode||40===a.keyCode)return void this.controller.trigger("attachment:keydown:arrow",a);if("keydown"!==a.type||13===a.keyCode||32===a.keyCode){if(a.preventDefault(),this.controller.isModeActive("grid")){if(this.controller.isModeActive("edit"))return void this.controller.trigger("edit:attachment",this.model,a.currentTarget);this.controller.isModeActive("select")&&(b="toggle")}a.shiftKey?b="between":(a.ctrlKey||a.metaKey)&&(b="toggle"),this.toggleSelection({method:b}),this.controller.trigger("selection:toggle")}}},toggleSelection:function(a){var b,c,d,e,f=this.collection,g=this.options.selection,h=this.model,i=a&&a.method;if(g){if(b=g.single(),i=_.isUndefined(i)?g.multiple:i,"between"===i&&b&&g.multiple){if(b===h)return;
        return d=f.indexOf(b),e=f.indexOf(this.model),c=d<e?f.models.slice(d,e+1):f.models.slice(e,d+1),g.add(c),void g.single(h)}if("toggle"===i)return g[this.selected()?"remove":"add"](h),void g.single(h);if("add"===i)return g.add(h),void g.single(h);i||(i="add"),"add"!==i&&(i="reset"),this.selected()?g[b===h?"remove":"single"](h):(g[i](h),g.single(h))}},updateSelect:function(){this[this.selected()?"select":"deselect"]()},selected:function(){var a=this.options.selection;if(a)return!!a.get(this.model.cid)},select:function(a,b){var c=this.options.selection,d=this.controller;!c||b&&b!==c||this.$el.hasClass("selected")||(this.$el.addClass("selected").attr("aria-checked",!0),d.isModeActive("grid")&&d.isModeActive("select")||this.$(".check").attr("tabindex","0"))},deselect:function(a,b){var c=this.options.selection;!c||b&&b!==c||this.$el.removeClass("selected").attr("aria-checked",!1).find(".check").attr("tabindex","-1")},details:function(a,b){var c,d=this.options.selection;d===b&&(c=d.single(),this.$el.toggleClass("details",c===this.model))},imageSize:function(a){var b=this.model.get("sizes"),c=!1;return a=a||"medium",b&&(b[a]?c=b[a]:b.large?c=b.large:b.thumbnail?c=b.thumbnail:b.full&&(c=b.full),c)?_.clone(c):{url:this.model.get("url"),width:this.model.get("width"),height:this.model.get("height"),orientation:this.model.get("orientation")}},updateSetting:function(a){var b,c,d=e(a.target).closest("[data-setting]");d.length&&(b=d.data("setting"),c=a.target.value,this.model.get(b)!==c&&this.save(b,c))},save:function(){var a=this,b=this._save=this._save||{status:"ready"},c=this.model.save.apply(this.model,arguments),d=b.requests?e.when(c,b.requests):c;b.savedTimer&&clearTimeout(b.savedTimer),this.updateSave("waiting"),b.requests=d,d.always(function(){b.requests===d&&(a.updateSave("resolved"===d.state()?"complete":"error"),b.savedTimer=setTimeout(function(){a.updateSave("ready"),delete b.savedTimer},2e3))})},updateSave:function(a){var b=this._save=this._save||{status:"ready"};return a&&a!==b.status&&(this.$el.removeClass("save-"+b.status),b.status=a),this.$el.addClass("save-"+b.status),this},updateAll:function(){var a,b=this.$("[data-setting]"),c=this.model;a=_.chain(b).map(function(a){var b,d,f=e("input, textarea, select, [value]",a);if(f.length)return b=e(a).data("setting"),d=f.val(),c.get(b)!==d?[b,d]:void 0}).compact().object().value(),_.isEmpty(a)||c.save(a)},removeFromLibrary:function(a){"keydown"===a.type&&13!==a.keyCode&&32!==a.keyCode||(a.stopPropagation(),this.collection.remove(this.model))},checkClickHandler:function(a){var b=this.options.selection;b&&(a.stopPropagation(),b.where({id:this.model.get("id")}).length?(b.remove(this.model),this.$el.focus()):b.add(this.model))}}),_.each({caption:"_syncCaption",title:"_syncTitle",artist:"_syncArtist",album:"_syncAlbum"},function(a,b){c.prototype[a]=function(a,c){var d=this.$('[data-setting="'+b+'"]');return d.length?c===d.find("input, textarea, select, [value]").val()?this:this.render():this}}),a.exports=c},function(a,b){var c=wp.media.view.Attachment.extend({buttons:{check:!0}});a.exports=c},function(a,b){var c=wp.media.view.Attachment.extend({buttons:{close:!0}});a.exports=c},function(a,b){var c,d=wp.media.View,e=jQuery;c=d.extend({tagName:"ul",className:"attachments",attributes:{tabIndex:-1},initialize:function(){this.el.id=_.uniqueId("__attachments-view-"),_.defaults(this.options,{refreshSensitivity:wp.media.isTouchDevice?300:200,refreshThreshold:3,AttachmentView:wp.media.view.Attachment,sortable:!1,resize:!0,idealColumnWidth:e(window).width()<640?135:150}),this._viewsByCid={},this.$window=e(window),this.resizeEvent="resize.media-modal-columns",this.collection.on("add",function(a){this.views.add(this.createAttachmentView(a),{at:this.collection.indexOf(a)})},this),this.collection.on("remove",function(a){var b=this._viewsByCid[a.cid];delete this._viewsByCid[a.cid],b&&b.remove()},this),this.collection.on("reset",this.render,this),this.listenTo(this.controller,"library:selection:add",this.attachmentFocus),this.scroll=_.chain(this.scroll).bind(this).throttle(this.options.refreshSensitivity).value(),this.options.scrollElement=this.options.scrollElement||this.el,e(this.options.scrollElement).on("scroll",this.scroll),this.initSortable(),_.bindAll(this,"setColumns"),this.options.resize&&(this.on("ready",this.bindEvents),this.controller.on("open",this.setColumns),_.defer(this.setColumns,this))},bindEvents:function(){this.$window.off(this.resizeEvent).on(this.resizeEvent,_.debounce(this.setColumns,50))},attachmentFocus:function(){this.$("li:first").focus()},restoreFocus:function(){this.$("li.selected:first").focus()},arrowEvent:function(a){var b=this.$el.children("li"),c=this.columns,d=b.filter(":focus").index(),e=d+1<=c?1:Math.ceil((d+1)/c);if(d!==-1){if(37===a.keyCode){if(0===d)return;b.eq(d-1).focus()}if(38===a.keyCode){if(1===e)return;b.eq(d-c).focus()}if(39===a.keyCode){if(b.length===d)return;b.eq(d+1).focus()}if(40===a.keyCode){if(Math.ceil(b.length/c)===e)return;b.eq(d+c).focus()}}},dispose:function(){this.collection.props.off(null,null,this),this.options.resize&&this.$window.off(this.resizeEvent),d.prototype.dispose.apply(this,arguments)},setColumns:function(){var a=this.columns,b=this.$el.width();b&&(this.columns=Math.min(Math.round(b/this.options.idealColumnWidth),12)||1,a&&a===this.columns||this.$el.closest(".media-frame-content").attr("data-columns",this.columns))},initSortable:function(){var a=this.collection;this.options.sortable&&e.fn.sortable&&(this.$el.sortable(_.extend({disabled:!!a.comparator,tolerance:"pointer",start:function(a,b){b.item.data("sortableIndexStart",b.item.index())},update:function(b,c){var d=a.at(c.item.data("sortableIndexStart")),e=a.comparator;delete a.comparator,a.remove(d,{silent:!0}),a.add(d,{silent:!0,at:c.item.index()}),a.comparator=e,a.trigger("reset",a),a.saveMenuOrder()}},this.options.sortable)),a.props.on("change:orderby",function(){this.$el.sortable("option","disabled",!!a.comparator)},this),this.collection.props.on("change:orderby",this.refreshSortable,this),this.refreshSortable())},refreshSortable:function(){if(this.options.sortable&&e.fn.sortable){var a=this.collection,b=a.props.get("orderby"),c="menuOrder"===b||!a.comparator;this.$el.sortable("option","disabled",!c)}},createAttachmentView:function(a){var b=new this.options.AttachmentView({controller:this.controller,model:a,collection:this.collection,selection:this.options.selection});return this._viewsByCid[a.cid]=b},prepare:function(){this.collection.length?this.views.set(this.collection.map(this.createAttachmentView,this)):(this.views.unset(),this.collection.more().done(this.scroll))},ready:function(){this.scroll()},scroll:function(){var a,b=this,c=this.options.scrollElement,d=c.scrollTop;c===document&&(c=document.body,d=e(document).scrollTop()),e(c).is(":visible")&&this.collection.hasMore()&&(a=this.views.parent.toolbar,c.scrollHeight-(d+c.clientHeight)<c.clientHeight/3&&a.get("spinner").show(),c.scrollHeight<d+c.clientHeight*this.options.refreshThreshold&&this.collection.more().done(function(){b.scroll(),a.get("spinner").hide()}))}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.View.extend({tagName:"input",className:"search",id:"media-search-input",attributes:{type:"search",placeholder:d.searchMediaPlaceholder},events:{input:"search",keyup:"search"},render:function(){return this.el.value=this.model.escape("search"),this},search:_.debounce(function(a){a.target.value?this.model.set("search",a.target.value):this.model.unset("search")},300)}),a.exports=c},function(a,b){var c,d=jQuery;c=wp.media.View.extend({tagName:"select",className:"attachment-filters",id:"media-attachment-filters",events:{change:"change"},keys:[],initialize:function(){this.createFilters(),_.extend(this.filters,this.options.filters),this.$el.html(_.chain(this.filters).map(function(a,b){return{el:d("<option></option>").val(b).html(a.text)[0],priority:a.priority||50}},this).sortBy("priority").pluck("el").value()),this.listenTo(this.model,"change",this.select),this.select()},createFilters:function(){this.filters={}},change:function(){var a=this.filters[this.el.value];a&&this.model.set(a.props)},select:function(){var a=this.model,b="all",c=a.toJSON();_.find(this.filters,function(a,d){var e=_.all(a.props,function(a,b){return a===(_.isUndefined(c[b])?null:c[b])});if(e)return b=d}),this.$el.val(b)}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.view.AttachmentFilters.extend({id:"media-attachment-date-filters",createFilters:function(){var a={};_.each(wp.media.view.settings.months||{},function(b,c){a[c]={text:b.text,props:{year:b.year,monthnum:b.month}}}),a.all={text:d.allDates,props:{monthnum:!1,year:!1},priority:10},this.filters=a}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.view.AttachmentFilters.extend({createFilters:function(){var a,b=this.model.get("type"),c=wp.media.view.settings.mimeTypes,e=window.userSettings?parseInt(window.userSettings.uid,10):0;c&&b&&(a=c[b]),this.filters={all:{text:a||d.allMediaItems,props:{uploadedTo:null,orderby:"date",order:"DESC",author:null},priority:10},uploaded:{text:d.uploadedToThisPost,props:{uploadedTo:wp.media.view.settings.post.id,orderby:"menuOrder",order:"ASC",author:null},priority:20},unattached:{text:d.unattached,props:{uploadedTo:0,orderby:"menuOrder",order:"ASC",author:null},priority:50}},e&&(this.filters.mine={text:d.mine,props:{orderby:"date",order:"DESC",author:e},priority:50})}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.view.AttachmentFilters.extend({createFilters:function(){var a={},b=window.userSettings?parseInt(window.userSettings.uid,10):0;_.each(wp.media.view.settings.mimeTypes||{},function(b,c){a[c]={text:b,props:{status:null,type:c,uploadedTo:null,orderby:"date",order:"DESC",author:null}}}),a.all={text:d.allMediaItems,props:{status:null,type:null,uploadedTo:null,orderby:"date",order:"DESC",author:null},priority:10},wp.media.view.settings.post.id&&(a.uploaded={text:d.uploadedToThisPost,props:{status:null,type:null,uploadedTo:wp.media.view.settings.post.id,orderby:"menuOrder",order:"ASC",author:null},priority:20}),a.unattached={text:d.unattached,props:{status:null,uploadedTo:0,type:null,orderby:"menuOrder",order:"ASC",author:null},priority:50},b&&(a.mine={text:d.mine,props:{status:null,type:null,uploadedTo:null,orderby:"date",order:"DESC",author:b},priority:50}),wp.media.view.settings.mediaTrash&&this.controller.isModeActive("grid")&&(a.trash={text:d.trash,props:{uploadedTo:null,status:"trash",type:null,orderby:"date",order:"DESC",author:null},priority:50}),this.filters=a}}),a.exports=c},function(a,b){var c,d=wp.media.View,e=wp.media.view.settings.mediaTrash,f=wp.media.view.l10n,g=jQuery;c=d.extend({tagName:"div",className:"attachments-browser",initialize:function(){_.defaults(this.options,{filters:!1,search:!0,date:!0,display:!1,sidebar:!0,AttachmentView:wp.media.view.Attachment.Library}),this.controller.on("toggle:upload:attachment",this.toggleUploader,this),this.controller.on("edit:selection",this.editSelection),this.options.sidebar&&"errors"===this.options.sidebar&&this.createSidebar(),this.createUploader(),this.createToolbar(),this.createAttachments(),this.options.sidebar&&"errors"!==this.options.sidebar&&this.createSidebar(),this.updateContent(),this.options.sidebar&&"errors"!==this.options.sidebar||(this.$el.addClass("hide-sidebar"),"errors"===this.options.sidebar&&this.$el.addClass("sidebar-for-errors")),this.collection.on("add remove reset",this.updateContent,this)},editSelection:function(a){a.$(".media-button-backToLibrary").focus()},dispose:function(){return this.options.selection.off(null,null,this),d.prototype.dispose.apply(this,arguments),this},createToolbar:function(){var a,b,c;c={controller:this.controller},this.controller.isModeActive("grid")&&(c.className="media-toolbar wp-filter"),this.toolbar=new wp.media.view.Toolbar(c),this.views.add(this.toolbar),this.toolbar.set("spinner",new wp.media.view.Spinner({priority:-60})),-1!==g.inArray(this.options.filters,["uploaded","all"])&&(this.toolbar.set("filtersLabel",new wp.media.view.Label({value:f.filterByType,attributes:{"for":"media-attachment-filters"},priority:-80}).render()),"uploaded"===this.options.filters?this.toolbar.set("filters",new wp.media.view.AttachmentFilters.Uploaded({controller:this.controller,model:this.collection.props,priority:-80}).render()):(b=new wp.media.view.AttachmentFilters.All({controller:this.controller,model:this.collection.props,priority:-80}),this.toolbar.set("filters",b.render()))),this.controller.isModeActive("grid")?(a=d.extend({className:"view-switch media-grid-view-switch",template:wp.template("media-library-view-switcher")}),this.toolbar.set("dateFilterLabel",new wp.media.view.Label({value:f.filterByDate,attributes:{"for":"media-attachment-date-filters"},priority:-75}).render()),this.toolbar.set("dateFilter",new wp.media.view.DateFilter({controller:this.controller,model:this.collection.props,priority:-75}).render()),this.toolbar.set("selectModeToggleButton",new wp.media.view.SelectModeToggleButton({text:f.bulkSelect,controller:this.controller,priority:-70}).render()),this.toolbar.set("deleteSelectedButton",new wp.media.view.DeleteSelectedButton({filters:b,style:"primary",disabled:!0,text:e?f.trashSelected:f.deleteSelected,controller:this.controller,priority:-60,click:function(){var a=[],b=[],c=this.controller.state().get("selection"),d=this.controller.state().get("library");c.length&&(e||window.confirm(f.warnBulkDelete))&&(e&&"trash"!==c.at(0).get("status")&&!window.confirm(f.warnBulkTrash)||(c.each(function(c){return c.get("nonces")["delete"]?void(e&&"trash"===c.get("status")?(c.set("status","inherit"),a.push(c.save()),b.push(c)):e?(c.set("status","trash"),a.push(c.save()),b.push(c)):c.destroy({wait:!0})):void b.push(c)}),a.length?(c.remove(b),g.when.apply(null,a).then(_.bind(function(){d._requery(!0),this.controller.trigger("selection:action:done")},this))):this.controller.trigger("selection:action:done")))}}).render()),e&&this.toolbar.set("deleteSelectedPermanentlyButton",new wp.media.view.DeleteSelectedPermanentlyButton({filters:b,style:"primary",disabled:!0,text:f.deleteSelected,controller:this.controller,priority:-55,click:function(){var a=[],b=[],c=this.controller.state().get("selection");c.length&&window.confirm(f.warnBulkDelete)&&(c.each(function(c){return c.get("nonces")["delete"]?void b.push(c):void a.push(c)}),a.length&&c.remove(a),b.length&&g.when.apply(null,b.map(function(a){return a.destroy()})).then(_.bind(function(){this.controller.trigger("selection:action:done")},this)))}}).render())):this.options.date&&(this.toolbar.set("dateFilterLabel",new wp.media.view.Label({value:f.filterByDate,attributes:{"for":"media-attachment-date-filters"},priority:-75}).render()),this.toolbar.set("dateFilter",new wp.media.view.DateFilter({controller:this.controller,model:this.collection.props,priority:-75}).render())),this.options.search&&(this.toolbar.set("searchLabel",new wp.media.view.Label({value:f.searchMediaLabel,attributes:{"for":"media-search-input"},priority:60}).render()),this.toolbar.set("search",new wp.media.view.Search({controller:this.controller,model:this.collection.props,priority:60}).render())),this.options.dragInfo&&this.toolbar.set("dragInfo",new d({el:g('<div class="instructions">'+f.dragInfo+"</div>")[0],priority:-40})),this.options.suggestedWidth&&this.options.suggestedHeight&&this.toolbar.set("suggestedDimensions",new d({el:g('<div class="instructions">'+f.suggestedDimensions.replace("%1$s",this.options.suggestedWidth).replace("%2$s",this.options.suggestedHeight)+"</div>")[0],priority:-40}))},updateContent:function(){var a,b=this;a=this.controller.isModeActive("grid")?b.attachmentsNoResults:b.uploader,this.collection.length?(a.$el.addClass("hidden"),b.toolbar.get("spinner").hide()):(this.toolbar.get("spinner").show(),this.dfd=this.collection.more().done(function(){b.collection.length?a.$el.addClass("hidden"):a.$el.removeClass("hidden"),b.toolbar.get("spinner").hide()}))},createUploader:function(){this.uploader=new wp.media.view.UploaderInline({controller:this.controller,status:!1,message:this.controller.isModeActive("grid")?"":f.noItemsFound,canClose:this.controller.isModeActive("grid")}),this.uploader.$el.addClass("hidden"),this.views.add(this.uploader)},toggleUploader:function(){this.uploader.$el.hasClass("hidden")?this.uploader.show():this.uploader.hide()},createAttachments:function(){this.attachments=new wp.media.view.Attachments({controller:this.controller,collection:this.collection,selection:this.options.selection,model:this.model,sortable:this.options.sortable,scrollElement:this.options.scrollElement,idealColumnWidth:this.options.idealColumnWidth,AttachmentView:this.options.AttachmentView}),this.controller.on("attachment:keydown:arrow",_.bind(this.attachments.arrowEvent,this.attachments)),this.controller.on("attachment:details:shift-tab",_.bind(this.attachments.restoreFocus,this.attachments)),this.views.add(this.attachments),this.controller.isModeActive("grid")&&(this.attachmentsNoResults=new d({controller:this.controller,tagName:"p"}),this.attachmentsNoResults.$el.addClass("hidden no-media"),this.attachmentsNoResults.$el.html(f.noMedia),this.views.add(this.attachmentsNoResults))},createSidebar:function(){var a=this.options,b=a.selection,c=this.sidebar=new wp.media.view.Sidebar({controller:this.controller});this.views.add(c),this.controller.uploader&&c.set("uploads",new wp.media.view.UploaderStatus({controller:this.controller,priority:40})),b.on("selection:single",this.createSingle,this),b.on("selection:unsingle",this.disposeSingle,this),b.single()&&this.createSingle()},createSingle:function(){var a=this.sidebar,b=this.options.selection.single();a.set("details",new wp.media.view.Attachment.Details({controller:this.controller,model:b,priority:80})),a.set("compat",new wp.media.view.AttachmentCompat({controller:this.controller,model:b,priority:120})),this.options.display&&a.set("display",new wp.media.view.Settings.AttachmentDisplay({controller:this.controller,model:this.model.display(b),attachment:b,priority:160,userSettings:this.model.get("displayUserSettings")})),"insert"===this.model.id&&a.$el.addClass("visible")},disposeSingle:function(){var a=this.sidebar;a.unset("details"),a.unset("compat"),a.unset("display"),a.$el.removeClass("visible")}}),a.exports=c},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.View.extend({tagName:"div",className:"media-selection",template:wp.template("media-selection"),events:{"click .edit-selection":"edit","click .clear-selection":"clear"},initialize:function(){_.defaults(this.options,{editable:!1,clearable:!0}),this.attachments=new wp.media.view.Attachments.Selection({controller:this.controller,collection:this.collection,selection:this.collection,model:new Backbone.Model}),this.views.set(".selection-view",this.attachments),this.collection.on("add remove reset",this.refresh,this),this.controller.on("content:activate",this.refresh,this)},ready:function(){this.refresh()},refresh:function(){if(this.$el.children().length){var a=this.collection,b="edit-selection"===this.controller.content.mode();this.$el.toggleClass("empty",!a.length),this.$el.toggleClass("one",1===a.length),this.$el.toggleClass("editing",b),this.$(".count").text(d.selected.replace("%d",a.length))}},edit:function(a){a.preventDefault(),this.options.editable&&this.options.editable.call(this,this.collection)},clear:function(a){a.preventDefault(),this.collection.reset(),this.controller.modal.focusManager.focus()}}),a.exports=c},function(a,b){var c=wp.media.view.Attachment.extend({className:"attachment selection",toggleSelection:function(){this.options.selection.single(this.model)}});a.exports=c},function(a,b){var c,d=wp.media.view.Attachments;c=d.extend({events:{},initialize:function(){return _.defaults(this.options,{sortable:!1,resize:!1,AttachmentView:wp.media.view.Attachment.Selection}),d.prototype.initialize.apply(this,arguments)}}),a.exports=c},function(a,b){var c=wp.media.view.Attachment.Selection.extend({buttons:{close:!0}});a.exports=c},function(a,b){var c,d=wp.media.View,e=Backbone.$;c=d.extend({events:{"click button":"updateHandler","change input":"updateHandler","change select":"updateHandler","change textarea":"updateHandler"},initialize:function(){this.model=this.model||new Backbone.Model,this.listenTo(this.model,"change",this.updateChanges)},prepare:function(){return _.defaults({model:this.model.toJSON()},this.options)},render:function(){return d.prototype.render.apply(this,arguments),_(this.model.attributes).chain().keys().each(this.update,this),this},update:function(a){var b,c,d=this.model.get(a),e=this.$('[data-setting="'+a+'"]');e.length&&(e.is("select")?(c=e.find('[value="'+d+'"]'),c.length?(e.find("option").prop("selected",!1),c.prop("selected",!0)):this.model.set(a,e.find(":selected").val())):e.hasClass("button-group")?(b=e.find("button").removeClass("active"),b.filter('[value="'+d+'"]').addClass("active")):e.is('input[type="text"], textarea')?e.is(":focus")||e.val(d):e.is('input[type="checkbox"]')&&e.prop("checked",!!d&&"false"!==d))},updateHandler:function(a){var b,c=e(a.target).closest("[data-setting]"),d=a.target.value;a.preventDefault(),c.length&&(c.is('input[type="checkbox"]')&&(d=c[0].checked),this.model.set(c.data("setting"),d),(b=c.data("userSetting"))&&window.setUserSetting(b,d))},updateChanges:function(a){a.hasChanged()&&_(a.changed).chain().keys().each(this.update,this)}}),a.exports=c},function(a,b){var c,d=wp.media.view.Settings;c=d.extend({className:"attachment-display-settings",template:wp.template("attachment-display-settings"),initialize:function(){var a=this.options.attachment;_.defaults(this.options,{userSettings:!1}),d.prototype.initialize.apply(this,arguments),this.listenTo(this.model,"change:link",this.updateLinkTo),a&&a.on("change:uploading",this.render,this)},dispose:function(){var a=this.options.attachment;a&&a.off(null,null,this),d.prototype.dispose.apply(this,arguments)},render:function(){var a=this.options.attachment;return a&&_.extend(this.options,{sizes:a.get("sizes"),type:a.get("type")}),d.prototype.render.call(this),this.updateLinkTo(),this},updateLinkTo:function(){var a=this.model.get("link"),b=this.$(".link-to-custom"),c=this.options.attachment;return"none"===a||"embed"===a||!c&&"custom"!==a?void b.addClass("hidden"):(c&&("post"===a?b.val(c.get("link")):"file"===a?b.val(c.get("url")):this.model.get("linkUrl")||b.val("http://"),b.prop("readonly","custom"!==a)),b.removeClass("hidden"),void(!wp.media.isTouchDevice&&b.is(":visible")&&b.focus()[0].select()))}}),a.exports=c},function(a,b){var c=wp.media.view.Settings.extend({className:"collection-settings gallery-settings",template:wp.template("gallery-settings")});a.exports=c},function(a,b){var c=wp.media.view.Settings.extend({className:"collection-settings playlist-settings",template:wp.template("playlist-settings")});a.exports=c},function(a,b){var c,d=wp.media.view.Attachment,e=wp.media.view.l10n;c=d.extend({tagName:"div",className:"attachment-details",template:wp.template("attachment-details"),attributes:function(){return{tabIndex:0,"data-id":this.model.get("id")}},events:{"change [data-setting]":"updateSetting","change [data-setting] input":"updateSetting","change [data-setting] select":"updateSetting","change [data-setting] textarea":"updateSetting","click .delete-attachment":"deleteAttachment","click .trash-attachment":"trashAttachment","click .untrash-attachment":"untrashAttachment","click .edit-attachment":"editAttachment",keydown:"toggleSelectionHandler"},initialize:function(){this.options=_.defaults(this.options,{rerenderOnModelChange:!1}),this.on("ready",this.initialFocus),d.prototype.initialize.apply(this,arguments)},initialFocus:function(){wp.media.isTouchDevice||this.$('input[type="text"]').eq(0).focus()},deleteAttachment:function(a){a.preventDefault(),window.confirm(e.warnDelete)&&(this.model.destroy(),this.controller.modal.focusManager.focus())},trashAttachment:function(a){var b=this.controller.library;a.preventDefault(),wp.media.view.settings.mediaTrash&&"edit-metadata"===this.controller.content.mode()?(this.model.set("status","trash"),this.model.save().done(function(){b._requery(!0)})):this.model.destroy()},untrashAttachment:function(a){var b=this.controller.library;a.preventDefault(),this.model.set("status","inherit"),this.model.save().done(function(){b._requery(!0)})},editAttachment:function(a){var b=this.controller.states.get("edit-image");window.imageEdit&&b?(a.preventDefault(),b.set("image",this.model),this.controller.setState("edit-image")):this.$el.addClass("needs-refresh")},toggleSelectionHandler:function(a){return"keydown"===a.type&&9===a.keyCode&&a.shiftKey&&a.target===this.$(":tabbable").get(0)?(this.controller.trigger("attachment:details:shift-tab",a),!1):37===a.keyCode||38===a.keyCode||39===a.keyCode||40===a.keyCode?void this.controller.trigger("attachment:keydown:arrow",a):void 0}}),a.exports=c},function(a,b){var c,d=wp.media.View;c=d.extend({tagName:"form",className:"compat-item",events:{submit:"preventDefault","change input":"save","change select":"save","change textarea":"save"},initialize:function(){this.listenTo(this.model,"change:compat",this.render)},dispose:function(){return this.$(":focus").length&&this.save(),d.prototype.dispose.apply(this,arguments)},render:function(){var a=this.model.get("compat");if(a&&a.item)return this.views.detach(),this.$el.html(a.item),this.views.render(),this},preventDefault:function(a){a.preventDefault()},save:function(a){var b={};a&&a.preventDefault(),_.each(this.$el.serializeArray(),function(a){b[a.name]=a.value}),this.controller.trigger("attachment:compat:waiting",["waiting"]),this.model.saveCompat(b).always(_.bind(this.postSave,this))},postSave:function(){this.controller.trigger("attachment:compat:ready",["ready"])}}),a.exports=c},function(a,b){var c=wp.media.View.extend({className:"media-iframe",render:function(){return this.views.detach(),this.$el.html('<iframe src="'+this.controller.state().get("src")+'" />'),this.views.render(),this}});a.exports=c},function(a,b){var c=wp.media.View.extend({className:"media-embed",initialize:function(){this.url=new wp.media.view.EmbedUrl({controller:this.controller,model:this.model.props}).render(),this.views.set([this.url]),this.refresh(),this.listenTo(this.model,"change:type",this.refresh),this.listenTo(this.model,"change:loading",this.loading)},settings:function(a){this._settings&&this._settings.remove(),this._settings=a,this.views.add(a)},refresh:function(){var a,b=this.model.get("type");if("image"===b)a=wp.media.view.EmbedImage;else{if("link"!==b)return;a=wp.media.view.EmbedLink}this.settings(new a({controller:this.controller,model:this.model.props,priority:40}))},loading:function(){this.$el.toggleClass("embed-loading",this.model.get("loading"))}});a.exports=c},function(a,b){var c=wp.media.View.extend({tagName:"label",className:"screen-reader-text",initialize:function(){this.value=this.options.value},render:function(){return this.$el.html(this.value),this}});a.exports=c},function(a,b){var c,d=wp.media.View,e=jQuery;c=d.extend({tagName:"label",className:"embed-url",events:{input:"url",keyup:"url",change:"url"},initialize:function(){this.$input=e('<input id="embed-url-field" type="url" />').val(this.model.get("url")),this.input=this.$input[0],this.spinner=e('<span class="spinner" />')[0],this.$el.append([this.input,this.spinner]),this.listenTo(this.model,"change:url",this.render),this.model.get("url")&&_.delay(_.bind(function(){this.model.trigger("change:url")},this),500)},render:function(){var a=this.$input;if(!a.is(":focus"))return this.input.value=this.model.get("url")||"http://",d.prototype.render.apply(this,arguments),this},ready:function(){wp.media.isTouchDevice||this.focus()},url:function(a){this.model.set("url",e.trim(a.target.value))},focus:function(){var a=this.$input;a.is(":visible")&&a.focus()[0].select()}}),a.exports=c},function(a,b){var c,d=jQuery;c=wp.media.view.Settings.extend({className:"embed-link-settings",template:wp.template("embed-link-settings"),initialize:function(){this.listenTo(this.model,"change:url",this.updateoEmbed)},updateoEmbed:_.debounce(function(){var a=this.model.get("url");this.$(".embed-container").hide().find(".embed-preview").empty(),this.$(".setting").hide(),a&&(a.length<11||!a.match(/^http(s)?:\/\//))||this.fetch()},wp.media.controller.Embed.sensitivity),fetch:function(){var a,b,c=this.model.get("url");d("#embed-url-field").val()===c&&(this.dfd&&"pending"===this.dfd.state()&&this.dfd.abort(),a=/https?:\/\/www\.youtube\.com\/embed\/([^\/]+)/,b=a.exec(c),b&&(c="https://www.youtube.com/watch?v="+b[1]),this.dfd=wp.apiRequest({url:wp.media.view.settings.oEmbedProxyUrl,data:{url:c,maxwidth:this.model.get("width"),maxheight:this.model.get("height")},type:"GET",dataType:"json",context:this}).done(function(a){this.renderoEmbed({data:{body:a.html||""}})}).fail(this.renderFail))},renderFail:function(a,b){"abort"!==b&&this.$(".link-text").show()},renderoEmbed:function(a){var b=a&&a.data&&a.data.body||"";b?this.$(".embed-container").show().find(".embed-preview").html(b):this.renderFail()}}),a.exports=c},function(a,b){var c,d=wp.media.view.Settings.AttachmentDisplay;c=d.extend({className:"embed-media-settings",template:wp.template("embed-image-settings"),initialize:function(){d.prototype.initialize.apply(this,arguments),this.listenTo(this.model,"change:url",this.updateImage)},updateImage:function(){this.$("img").attr("src",this.model.get("url"))}}),a.exports=c},function(a,b){var c,d=wp.media.view.Settings.AttachmentDisplay,e=jQuery;c=d.extend({className:"image-details",template:wp.template("image-details"),events:_.defaults(d.prototype.events,{"click .edit-attachment":"editAttachment","click .replace-attachment":"replaceAttachment","click .advanced-toggle":"onToggleAdvanced",'change [data-setting="customWidth"]':"onCustomSize",'change [data-setting="customHeight"]':"onCustomSize",'keyup [data-setting="customWidth"]':"onCustomSize",'keyup [data-setting="customHeight"]':"onCustomSize"}),initialize:function(){this.options.attachment=this.model.attachment,this.listenTo(this.model,"change:url",this.updateUrl),this.listenTo(this.model,"change:link",this.toggleLinkSettings),this.listenTo(this.model,"change:size",this.toggleCustomSize),d.prototype.initialize.apply(this,arguments)},prepare:function(){var a=!1;return this.model.attachment&&(a=this.model.attachment.toJSON()),_.defaults({model:this.model.toJSON(),attachment:a},this.options)},render:function(){var a=arguments;return this.model.attachment&&"pending"===this.model.dfd.state()?this.model.dfd.done(_.bind(function(){d.prototype.render.apply(this,a),this.postRender()},this)).fail(_.bind(function(){this.model.attachment=!1,d.prototype.render.apply(this,a),this.postRender()},this)):(d.prototype.render.apply(this,arguments),this.postRender()),this},postRender:function(){setTimeout(_.bind(this.resetFocus,this),10),this.toggleLinkSettings(),"show"===window.getUserSetting("advImgDetails")&&this.toggleAdvanced(!0),this.trigger("post-render")},resetFocus:function(){this.$(".link-to-custom").blur(),this.$(".embed-media-settings").scrollTop(0)},updateUrl:function(){this.$(".image img").attr("src",this.model.get("url")),this.$(".url").val(this.model.get("url"))},toggleLinkSettings:function(){"none"===this.model.get("link")?this.$(".link-settings").addClass("hidden"):this.$(".link-settings").removeClass("hidden")},toggleCustomSize:function(){"custom"!==this.model.get("size")?this.$(".custom-size").addClass("hidden"):this.$(".custom-size").removeClass("hidden")},onCustomSize:function(a){var b,c=e(a.target).data("setting"),d=e(a.target).val();return!/^\d+/.test(d)||parseInt(d,10)<1?void a.preventDefault():void("customWidth"===c?(b=Math.round(1/this.model.get("aspectRatio")*d),this.model.set("customHeight",b,{silent:!0}),this.$('[data-setting="customHeight"]').val(b)):(b=Math.round(this.model.get("aspectRatio")*d),
        this.model.set("customWidth",b,{silent:!0}),this.$('[data-setting="customWidth"]').val(b)))},onToggleAdvanced:function(a){a.preventDefault(),this.toggleAdvanced()},toggleAdvanced:function(a){var b,c=this.$el.find(".advanced-section");c.hasClass("advanced-visible")||a===!1?(c.removeClass("advanced-visible"),c.find(".advanced-settings").addClass("hidden"),b="hide"):(c.addClass("advanced-visible"),c.find(".advanced-settings").removeClass("hidden"),b="show"),window.setUserSetting("advImgDetails",b)},editAttachment:function(a){var b=this.controller.states.get("edit-image");window.imageEdit&&b&&(a.preventDefault(),b.set("image",this.model.attachment),this.controller.setState("edit-image"))},replaceAttachment:function(a){a.preventDefault(),this.controller.setState("replace-image")}}),a.exports=c},function(a,b){var c,d=wp.media.View,e=wp.media.view.UploaderStatus,f=wp.media.view.l10n,g=jQuery;c=d.extend({className:"crop-content",template:wp.template("crop-content"),initialize:function(){_.bindAll(this,"onImageLoad")},ready:function(){this.controller.frame.on("content:error:crop",this.onError,this),this.$image=this.$el.find(".crop-image"),this.$image.on("load",this.onImageLoad),g(window).on("resize.cropper",_.debounce(this.onImageLoad,250))},remove:function(){g(window).off("resize.cropper"),this.$el.remove(),this.$el.off(),d.prototype.remove.apply(this,arguments)},prepare:function(){return{title:f.cropYourImage,url:this.options.attachment.get("url")}},onImageLoad:function(){var a,b=this.controller.get("imgSelectOptions");"function"==typeof b&&(b=b(this.options.attachment,this.controller)),b=_.extend(b,{parent:this.$el,onInit:function(){var b=a.getOptions().aspectRatio;this.parent.children().on("mousedown touchstart",function(c){!b&&c.shiftKey&&a.setOptions({aspectRatio:"1:1"})}),this.parent.children().on("mouseup touchend",function(){a.setOptions({aspectRatio:!!b&&b})})}}),this.trigger("image-loaded"),a=this.controller.imgSelect=this.$image.imgAreaSelect(b)},onError:function(){var a=this.options.attachment.get("filename");this.views.add(".upload-errors",new wp.media.view.UploaderStatusError({filename:e.prototype.filename(a),message:window._wpMediaViewsL10n.cropError}),{at:0})}}),a.exports=c},function(a,b){var c,d=wp.media.view;c=d.Cropper.extend({className:"crop-content site-icon",ready:function(){d.Cropper.prototype.ready.apply(this,arguments),this.$(".crop-image").on("load",_.bind(this.addSidebar,this))},addSidebar:function(){this.sidebar=new wp.media.view.Sidebar({controller:this.controller}),this.sidebar.set("preview",new wp.media.view.SiteIconPreview({controller:this.controller,attachment:this.options.attachment})),this.controller.cropperView.views.add(this.sidebar)}}),a.exports=c},function(a,b){var c,d=wp.media.View,e=jQuery;c=d.extend({className:"site-icon-preview",template:wp.template("site-icon-preview"),ready:function(){this.controller.imgSelect.setOptions({onInit:this.updatePreview,onSelectChange:this.updatePreview})},prepare:function(){return{url:this.options.attachment.get("url")}},updatePreview:function(a,b){var c=64/b.width,d=64/b.height,f=16/b.width,g=16/b.height;e("#preview-app-icon").css({width:Math.round(c*this.imageWidth)+"px",height:Math.round(d*this.imageHeight)+"px",marginLeft:"-"+Math.round(c*b.x1)+"px",marginTop:"-"+Math.round(d*b.y1)+"px"}),e("#preview-favicon").css({width:Math.round(f*this.imageWidth)+"px",height:Math.round(g*this.imageHeight)+"px",marginLeft:"-"+Math.round(f*b.x1)+"px",marginTop:"-"+Math.floor(g*b.y1)+"px"})}}),a.exports=c},function(a,b){var c,d=wp.media.View;c=d.extend({className:"image-editor",template:wp.template("image-editor"),initialize:function(a){this.editor=window.imageEdit,this.controller=a.controller,d.prototype.initialize.apply(this,arguments)},prepare:function(){return this.model.toJSON()},loadEditor:function(){var a=this.editor.open(this.model.get("id"),this.model.get("nonces").edit,this);a.done(_.bind(this.focus,this))},focus:function(){this.$(".imgedit-submit .button").eq(0).focus()},back:function(){var a=this.controller.lastState();this.controller.setState(a)},refresh:function(){this.model.fetch()},save:function(){var a=this.controller.lastState();this.model.fetch().done(_.bind(function(){this.controller.setState(a)},this))}}),a.exports=c},function(a,b){var c=wp.media.View.extend({tagName:"span",className:"spinner",spinnerTimeout:!1,delay:400,show:function(){return this.spinnerTimeout||(this.spinnerTimeout=_.delay(function(a){a.addClass("is-active")},this.delay,this.$el)),this},hide:function(){return this.$el.removeClass("is-active"),this.spinnerTimeout=clearTimeout(this.spinnerTimeout),this}});a.exports=c}]));
!function(a,b){var c={};wp.media.coerce=function(a,c){return b.isUndefined(a[c])&&!b.isUndefined(this.defaults[c])?a[c]=this.defaults[c]:"true"===a[c]?a[c]=!0:"false"===a[c]&&(a[c]=!1),a[c]},wp.media.string={props:function(a,c){var d,e,f,g,h=wp.media.view.settings.defaultProps;return a=a?b.clone(a):{},c&&c.type&&(a.type=c.type),"image"===a.type&&(a=b.defaults(a||{},{align:h.align||getUserSetting("align","none"),size:h.size||getUserSetting("imgsize","medium"),url:"",classes:[]})),c?(a.title=a.title||c.title,d=a.link||h.link||getUserSetting("urlbutton","file"),"file"===d||"embed"===d?e=c.url:"post"===d?e=c.link:"custom"===d&&(e=a.linkUrl),a.linkUrl=e||"","image"===c.type?(a.classes.push("wp-image-"+c.id),g=c.sizes,f=g&&g[a.size]?g[a.size]:c,b.extend(a,b.pick(c,"align","caption","alt"),{width:f.width,height:f.height,src:f.url,captionId:"attachment_"+c.id})):"video"===c.type||"audio"===c.type?b.extend(a,b.pick(c,"title","type","icon","mime")):(a.title=a.title||c.filename,a.rel=a.rel||"attachment wp-att-"+c.id),a):a},link:function(a,b){var c;return a=wp.media.string.props(a,b),c={tag:"a",content:a.title,attrs:{href:a.linkUrl}},a.rel&&(c.attrs.rel=a.rel),wp.html.string(c)},audio:function(a,b){return wp.media.string._audioVideo("audio",a,b)},video:function(a,b){return wp.media.string._audioVideo("video",a,b)},_audioVideo:function(a,c,d){var e,f,g;return c=wp.media.string.props(c,d),"embed"!==c.link?wp.media.string.link(c):(e={},"video"===a&&(d.image&&-1===d.image.src.indexOf(d.icon)&&(e.poster=d.image.src),d.width&&(e.width=d.width),d.height&&(e.height=d.height)),g=d.filename.split(".").pop(),b.contains(wp.media.view.settings.embedExts,g)?(e[g]=d.url,f=wp.shortcode.string({tag:a,attrs:e})):wp.media.string.link(c))},image:function(a,c){var d,e,f,g,h={};return a.type="image",a=wp.media.string.props(a,c),e=a.classes||[],h.src=b.isUndefined(c)?a.url:c.url,b.extend(h,b.pick(a,"width","height","alt")),a.align&&!a.caption&&e.push("align"+a.align),a.size&&e.push("size-"+a.size),h["class"]=b.compact(e).join(" "),d={tag:"img",attrs:h,single:!0},a.linkUrl&&(d={tag:"a",attrs:{href:a.linkUrl},content:d}),g=wp.html.string(d),a.caption&&(f={},h.width&&(f.width=h.width),a.captionId&&(f.id=a.captionId),a.align&&(f.align="align"+a.align),g=wp.shortcode.string({tag:"caption",attrs:f,content:g+" "+a.caption})),g}},wp.media.embed={coerce:wp.media.coerce,defaults:{url:"",width:"",height:""},edit:function(a,c){var d,e,f={};return c?f.url=a.replace(/<[^>]+>/g,""):(e=wp.shortcode.next("embed",a).shortcode,f=b.defaults(e.attrs.named,this.defaults),e.content&&(f.url=e.content)),d=wp.media({frame:"post",state:"embed",metadata:f})},shortcode:function(a){var c,d=this;return b.each(this.defaults,function(b,c){a[c]=d.coerce(a,c),b===a[c]&&delete a[c]}),c=a.url,delete a.url,new wp.shortcode({tag:"embed",attrs:a,content:c})}},wp.media.collection=function(a){var c={};return b.extend({coerce:wp.media.coerce,attachments:function(a){var d,e,f,g,h=a.string(),i=c[h],j=this;return delete c[h],i?i:(d=b.defaults(a.attrs.named,this.defaults),e=b.pick(d,"orderby","order"),e.type=this.type,e.perPage=-1,void 0!==d.orderby&&(d._orderByField=d.orderby),"rand"===d.orderby&&(d._orderbyRandom=!0),d.orderby&&!/^menu_order(?: ID)?$/i.test(d.orderby)||(e.orderby="menuOrder"),d.ids?(e.post__in=d.ids.split(","),e.orderby="post__in"):d.include&&(e.post__in=d.include.split(",")),d.exclude&&(e.post__not_in=d.exclude.split(",")),e.post__in||(e.uploadedTo=d.id),g=b.omit(d,"id","ids","include","exclude","orderby","order"),b.each(this.defaults,function(a,b){g[b]=j.coerce(g,b)}),f=wp.media.query(e),f[this.tag]=new Backbone.Model(g),f)},shortcode:function(a){var d,e,f=a.props.toJSON(),g=b.pick(f,"orderby","order");return a.type&&(g.type=a.type,delete a.type),a[this.tag]&&b.extend(g,a[this.tag].toJSON()),g.ids=a.pluck("id"),f.uploadedTo&&(g.id=f.uploadedTo),delete g.orderby,g._orderbyRandom?g.orderby="rand":g._orderByField&&"rand"!=g._orderByField&&(g.orderby=g._orderByField),delete g._orderbyRandom,delete g._orderByField,g.ids&&"post__in"===g.orderby&&delete g.orderby,g=this.setDefaults(g),d=new wp.shortcode({tag:this.tag,attrs:g,type:"single"}),e=new wp.media.model.Attachments(a.models,{props:f}),e[this.tag]=a[this.tag],c[d.string()]=e,d},edit:function(a){var c,d,e,f=wp.shortcode.next(this.tag,a),g=this.defaults.id;if(f&&f.content===a)return f=f.shortcode,b.isUndefined(f.get("id"))&&!b.isUndefined(g)&&f.set("id",g),c=this.attachments(f),d=new wp.media.model.Selection(c.models,{props:c.props.toJSON(),multiple:!0}),d[this.tag]=c[this.tag],d.more().done(function(){d.props.set({query:!1}),d.unmirror(),d.props.unset("orderby")}),this.frame&&this.frame.dispose(),e=f.attrs.named.type&&"video"===f.attrs.named.type?"video-"+this.tag+"-edit":this.tag+"-edit",this.frame=wp.media({frame:"post",state:e,title:this.editTitle,editing:!0,multiple:!0,selection:d}).open(),this.frame},setDefaults:function(a){var c=this;return b.each(this.defaults,function(b,d){a[d]=c.coerce(a,d),b===a[d]&&delete a[d]}),a}},a)},wp.media._galleryDefaults={itemtag:"dl",icontag:"dt",captiontag:"dd",columns:"3",link:"post",size:"thumbnail",order:"ASC",id:wp.media.view.settings.post&&wp.media.view.settings.post.id,orderby:"menu_order ID"},wp.media.view.settings.galleryDefaults?wp.media.galleryDefaults=b.extend({},wp.media._galleryDefaults,wp.media.view.settings.galleryDefaults):wp.media.galleryDefaults=wp.media._galleryDefaults,wp.media.gallery=new wp.media.collection({tag:"gallery",type:"image",editTitle:wp.media.view.l10n.editGalleryTitle,defaults:wp.media.galleryDefaults,setDefaults:function(a){var c=this,d=!b.isEqual(wp.media.galleryDefaults,wp.media._galleryDefaults);return b.each(this.defaults,function(b,e){a[e]=c.coerce(a,e),b!==a[e]||d&&b!==wp.media._galleryDefaults[e]||delete a[e]}),a}}),wp.media.featuredImage={get:function(){return wp.media.view.settings.post.featuredImageId},set:function(b){var c=wp.media.view.settings;c.post.featuredImageId=b,wp.media.post("get-post-thumbnail-html",{post_id:c.post.id,thumbnail_id:c.post.featuredImageId,_wpnonce:c.post.nonce}).done(function(b){return"0"==b?void window.alert(window.setPostThumbnailL10n.error):void a(".inside","#postimagediv").html(b)})},remove:function(){wp.media.featuredImage.set(-1)},frame:function(){return this._frame?(wp.media.frame=this._frame,this._frame):(this._frame=wp.media({state:"featured-image",states:[new wp.media.controller.FeaturedImage,new wp.media.controller.EditImage]}),this._frame.on("toolbar:create:featured-image",function(a){this.createSelectToolbar(a,{text:wp.media.view.l10n.setFeaturedImage})},this._frame),this._frame.on("content:render:edit-image",function(){var a=this.state("featured-image").get("selection"),b=new wp.media.view.EditImage({model:a.single(),controller:this}).render();this.content.set(b),b.loadEditor()},this._frame),this._frame.state("featured-image").on("select",this.select),this._frame)},select:function(){var a=this.get("selection").single();wp.media.view.settings.post.featuredImageId&&wp.media.featuredImage.set(a?a.id:-1)},init:function(){a("#postimagediv").on("click","#set-post-thumbnail",function(a){a.preventDefault(),a.stopPropagation(),wp.media.featuredImage.frame().open()}).on("click","#remove-post-thumbnail",function(){return wp.media.featuredImage.remove(),!1})}},a(wp.media.featuredImage.init),wp.media.editor={insert:function(a){var c,d,e=!b.isUndefined(window.tinymce),f=!b.isUndefined(window.QTags);if(d=this.activeEditor?window.wpActiveEditor=this.activeEditor:window.wpActiveEditor,window.send_to_editor)return window.send_to_editor.apply(this,arguments);if(d)e&&(c=tinymce.get(d));else if(e&&tinymce.activeEditor)c=tinymce.activeEditor,d=window.wpActiveEditor=c.id;else if(!f)return!1;if(c&&!c.isHidden()?c.execCommand("mceInsertContent",!1,a):f?QTags.insertContent(a):document.getElementById(d).value+=a,window.tb_remove)try{window.tb_remove()}catch(g){}},add:function(d,e){var f=this.get(d);return f?f:(f=c[d]=wp.media(b.defaults(e||{},{frame:"post",state:"insert",title:wp.media.view.l10n.addMedia,multiple:!0})),f.on("insert",function(c){var d=f.state();c=c||d.get("selection"),c&&a.when.apply(a,c.map(function(a){var b=d.display(a).toJSON();return this.send.attachment(b,a.toJSON())},this)).done(function(){wp.media.editor.insert(b.toArray(arguments).join("\n\n"))})},this),f.state("gallery-edit").on("update",function(a){this.insert(wp.media.gallery.shortcode(a).string())},this),f.state("playlist-edit").on("update",function(a){this.insert(wp.media.playlist.shortcode(a).string())},this),f.state("video-playlist-edit").on("update",function(a){this.insert(wp.media.playlist.shortcode(a).string())},this),f.state("embed").on("select",function(){var a=f.state(),c=a.get("type"),d=a.props.toJSON();d.url=d.url||"","link"===c?(b.defaults(d,{linkText:d.url,linkUrl:d.url}),this.send.link(d).done(function(a){wp.media.editor.insert(a)})):"image"===c&&(b.defaults(d,{title:d.url,linkUrl:"",align:"none",link:"none"}),"none"===d.link?d.linkUrl="":"file"===d.link&&(d.linkUrl=d.url),this.insert(wp.media.string.image(d)))},this),f.state("featured-image").on("select",wp.media.featuredImage.select),f.setState(f.options.state),f)},id:function(a){return a?a:(a=window.wpActiveEditor,a||b.isUndefined(window.tinymce)||!tinymce.activeEditor||(a=tinymce.activeEditor.id),a=a||"")},get:function(a){return a=this.id(a),c[a]},remove:function(a){a=this.id(a),delete c[a]},send:{attachment:function(a,c){var d,e,f=c.caption;return wp.media.view.settings.captions||delete c.caption,a=wp.media.string.props(a,c),d={id:c.id,post_content:c.description,post_excerpt:f},a.linkUrl&&(d.url=a.linkUrl),"image"===c.type?(e=wp.media.string.image(a),b.each({align:"align",size:"image-size",alt:"image_alt"},function(b,c){a[c]&&(d[b]=a[c])})):"video"===c.type?e=wp.media.string.video(a,c):"audio"===c.type?e=wp.media.string.audio(a,c):(e=wp.media.string.link(a),d.post_title=a.title),wp.media.post("send-attachment-to-editor",{nonce:wp.media.view.settings.nonce.sendToEditor,attachment:d,html:e,post_id:wp.media.view.settings.post.id})},link:function(a){return wp.media.post("send-link-to-editor",{nonce:wp.media.view.settings.nonce.sendToEditor,src:a.linkUrl,link_text:a.linkText,html:wp.media.string.link(a),post_id:wp.media.view.settings.post.id})}},open:function(a,b){var c;return b=b||{},a=this.id(a),this.activeEditor=a,c=this.get(a),(!c||c.options&&b.state!==c.options.state)&&(c=this.add(a,b)),wp.media.frame=c,c.open()},init:function(){a(document.body).on("click.add-media-button",".insert-media",function(b){var c=a(b.currentTarget),d=c.data("editor"),e={frame:"post",state:"insert",title:wp.media.view.l10n.addMedia,multiple:!0};b.preventDefault(),c.hasClass("gallery")&&(e.state="gallery",e.title=wp.media.view.l10n.createGalleryTitle),wp.media.editor.open(d,e)}),(new wp.media.view.EditorUploader).render()}},b.bindAll(wp.media.editor,"open"),a(wp.media.editor.init)}(jQuery,_);
!function(a){function b(d){if(c[d])return c[d].exports;var e=c[d]={i:d,l:!1,exports:{}};return a[d].call(e.exports,e,e.exports,b),e.l=!0,e.exports}var c={};return b.m=a,b.c=c,b.d=function(a,c,d){b.o(a,c)||Object.defineProperty(a,c,{configurable:!1,enumerable:!0,get:d})},b.n=function(a){var c=a&&a.__esModule?function(){return a["default"]}:function(){return a};return b.d(c,"a",c),c},b.o=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)},b.p="",b(b.s=0)}([function(a,b,c){var d=wp.media,e=window._wpmejsSettings||{},f=window._wpMediaViewsL10n||{};wp.media.mixin={mejsSettings:e,removeAllPlayers:function(){var a;if(window.mejs&&window.mejs.players)for(a in window.mejs.players)window.mejs.players[a].pause(),this.removePlayer(window.mejs.players[a])},removePlayer:function(a){var b,c;if(a.options){for(b in a.options.features)if(c=a.options.features[b],a["clean"+c])try{a["clean"+c](a)}catch(d){}a.isDynamic||a.node.remove(),"html5"!==a.media.rendererName&&a.media.remove(),delete window.mejs.players[a.id],a.container.remove(),a.globalUnbind("resize",a.globalResizeCallback),a.globalUnbind("keydown",a.globalKeydownCallback),a.globalUnbind("click",a.globalClickCallback),delete a.media.player}},unsetPlayers:function(){this.players&&this.players.length&&(_.each(this.players,function(a){a.pause(),wp.media.mixin.removePlayer(a)}),this.players=[])}},wp.media.playlist=new wp.media.collection({tag:"playlist",editTitle:f.editPlaylistTitle,defaults:{id:wp.media.view.settings.post.id,style:"light",tracklist:!0,tracknumbers:!0,images:!0,artists:!0,type:"audio"}}),wp.media.audio={coerce:wp.media.coerce,defaults:{id:wp.media.view.settings.post.id,src:"",loop:!1,autoplay:!1,preload:"none",width:400},edit:function(a){var b,c=wp.shortcode.next("audio",a).shortcode;return b=wp.media({frame:"audio",state:"audio-details",metadata:_.defaults(c.attrs.named,this.defaults)})},shortcode:function(a){var b;return _.each(this.defaults,function(b,c){a[c]=this.coerce(a,c),b===a[c]&&delete a[c]},this),b=a.content,delete a.content,new wp.shortcode({tag:"audio",attrs:a,content:b})}},wp.media.video={coerce:wp.media.coerce,defaults:{id:wp.media.view.settings.post.id,src:"",poster:"",loop:!1,autoplay:!1,preload:"metadata",content:"",width:640,height:360},edit:function(a){var b,c,d=wp.shortcode.next("video",a).shortcode;return c=d.attrs.named,c.content=d.content,b=wp.media({frame:"video",state:"video-details",metadata:_.defaults(c,this.defaults)})},shortcode:function(a){var b;return _.each(this.defaults,function(b,c){a[c]=this.coerce(a,c),b===a[c]&&delete a[c]},this),b=a.content,delete a.content,new wp.shortcode({tag:"video",attrs:a,content:b})}},d.model.PostMedia=c(1),d.controller.AudioDetails=c(2),d.controller.VideoDetails=c(3),d.view.MediaFrame.MediaDetails=c(4),d.view.MediaFrame.AudioDetails=c(5),d.view.MediaFrame.VideoDetails=c(6),d.view.MediaDetails=c(7),d.view.AudioDetails=c(8),d.view.VideoDetails=c(9)},function(a,b){var c=Backbone.Model.extend({initialize:function(){this.attachment=!1},setSource:function(a){this.attachment=a,this.extension=a.get("filename").split(".").pop(),this.get("src")&&this.extension===this.get("src").split(".").pop()&&this.unset("src"),_.contains(wp.media.view.settings.embedExts,this.extension)?this.set(this.extension,this.attachment.get("url")):this.unset(this.extension)},changeAttachment:function(a){this.setSource(a),this.unset("src"),_.each(_.without(wp.media.view.settings.embedExts,this.extension),function(a){this.unset(a)},this)}});a.exports=c},function(a,b){var c,d=wp.media.controller.State,e=wp.media.view.l10n;c=d.extend({defaults:{id:"audio-details",toolbar:"audio-details",title:e.audioDetailsTitle,content:"audio-details",menu:"audio-details",router:!1,priority:60},initialize:function(a){this.media=a.media,d.prototype.initialize.apply(this,arguments)}}),a.exports=c},function(a,b){var c,d=wp.media.controller.State,e=wp.media.view.l10n;c=d.extend({defaults:{id:"video-details",toolbar:"video-details",title:e.videoDetailsTitle,content:"video-details",menu:"video-details",router:!1,priority:60},initialize:function(a){this.media=a.media,d.prototype.initialize.apply(this,arguments)}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame.Select,e=wp.media.view.l10n;c=d.extend({defaults:{id:"media",url:"",menu:"media-details",content:"media-details",toolbar:"media-details",type:"link",priority:120},initialize:function(a){this.DetailsView=a.DetailsView,this.cancelText=a.cancelText,this.addText=a.addText,this.media=new wp.media.model.PostMedia(a.metadata),this.options.selection=new wp.media.model.Selection(this.media.attachment,{multiple:!1}),d.prototype.initialize.apply(this,arguments)},bindHandlers:function(){var a=this.defaults.menu;d.prototype.bindHandlers.apply(this,arguments),this.on("menu:create:"+a,this.createMenu,this),this.on("content:render:"+a,this.renderDetailsContent,this),this.on("menu:render:"+a,this.renderMenu,this),this.on("toolbar:render:"+a,this.renderDetailsToolbar,this)},renderDetailsContent:function(){var a=new this.DetailsView({controller:this,model:this.state().media,attachment:this.state().media.attachment}).render();this.content.set(a)},renderMenu:function(a){var b=this.lastState(),c=b&&b.id,d=this;a.set({cancel:{text:this.cancelText,priority:20,click:function(){c?d.setState(c):d.close()}},separateCancel:new wp.media.View({className:"separator",priority:40})})},setPrimaryButton:function(a,b){this.toolbar.set(new wp.media.view.Toolbar({controller:this,items:{button:{style:"primary",text:a,priority:80,click:function(){var a=this.controller;b.call(this,a,a.state()),a.setState(a.options.state),a.reset()}}}}))},renderDetailsToolbar:function(){this.setPrimaryButton(e.update,function(a,b){a.close(),b.trigger("update",a.media.toJSON())})},renderReplaceToolbar:function(){this.setPrimaryButton(e.replace,function(a,b){var c=b.get("selection").single();a.media.changeAttachment(c),b.trigger("replace",a.media.toJSON())})},renderAddSourceToolbar:function(){this.setPrimaryButton(this.addText,function(a,b){var c=b.get("selection").single();a.media.setSource(c),b.trigger("add-source",a.media.toJSON())})}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame.MediaDetails,e=wp.media.controller.MediaLibrary,f=wp.media.view.l10n;c=d.extend({defaults:{id:"audio",url:"",menu:"audio-details",content:"audio-details",toolbar:"audio-details",type:"link",title:f.audioDetailsTitle,priority:120},initialize:function(a){a.DetailsView=wp.media.view.AudioDetails,a.cancelText=f.audioDetailsCancel,a.addText=f.audioAddSourceTitle,d.prototype.initialize.call(this,a)},bindHandlers:function(){d.prototype.bindHandlers.apply(this,arguments),this.on("toolbar:render:replace-audio",this.renderReplaceToolbar,this),this.on("toolbar:render:add-audio-source",this.renderAddSourceToolbar,this)},createStates:function(){this.states.add([new wp.media.controller.AudioDetails({media:this.media}),new e({type:"audio",id:"replace-audio",title:f.audioReplaceTitle,toolbar:"replace-audio",media:this.media,menu:"audio-details"}),new e({type:"audio",id:"add-audio-source",title:f.audioAddSourceTitle,toolbar:"add-audio-source",media:this.media,menu:!1})])}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame.MediaDetails,e=wp.media.controller.MediaLibrary,f=wp.media.view.l10n;c=d.extend({defaults:{id:"video",url:"",menu:"video-details",content:"video-details",toolbar:"video-details",type:"link",title:f.videoDetailsTitle,priority:120},initialize:function(a){a.DetailsView=wp.media.view.VideoDetails,a.cancelText=f.videoDetailsCancel,a.addText=f.videoAddSourceTitle,d.prototype.initialize.call(this,a)},bindHandlers:function(){d.prototype.bindHandlers.apply(this,arguments),this.on("toolbar:render:replace-video",this.renderReplaceToolbar,this),this.on("toolbar:render:add-video-source",this.renderAddSourceToolbar,this),this.on("toolbar:render:select-poster-image",this.renderSelectPosterImageToolbar,this),this.on("toolbar:render:add-track",this.renderAddTrackToolbar,this)},createStates:function(){this.states.add([new wp.media.controller.VideoDetails({media:this.media}),new e({type:"video",id:"replace-video",title:f.videoReplaceTitle,toolbar:"replace-video",media:this.media,menu:"video-details"}),new e({type:"video",id:"add-video-source",title:f.videoAddSourceTitle,toolbar:"add-video-source",media:this.media,menu:!1}),new e({type:"image",id:"select-poster-image",title:f.videoSelectPosterImageTitle,toolbar:"select-poster-image",media:this.media,menu:"video-details"}),new e({type:"text",id:"add-track",title:f.videoAddTrackTitle,toolbar:"add-track",media:this.media,menu:"video-details"})])},renderSelectPosterImageToolbar:function(){this.setPrimaryButton(f.videoSelectPosterImageTitle,function(a,b){var c=[],d=b.get("selection").single();a.media.set("poster",d.get("url")),b.trigger("set-poster-image",a.media.toJSON()),_.each(wp.media.view.settings.embedExts,function(b){a.media.get(b)&&c.push(a.media.get(b))}),wp.ajax.send("set-attachment-thumbnail",{data:{urls:c,thumbnail_id:d.get("id")}})})},renderAddTrackToolbar:function(){this.setPrimaryButton(f.videoAddTrackTitle,function(a,b){var c=b.get("selection").single(),d=a.media.get("content");-1===d.indexOf(c.get("url"))&&(d+=['<track srclang="en" label="English" kind="subtitles" src="',c.get("url"),'" />'].join(""),a.media.set("content",d)),b.trigger("add-track",a.media.toJSON())})}}),a.exports=c},function(a,b){var c,d=wp.media.view.Settings.AttachmentDisplay,e=jQuery;c=d.extend({initialize:function(){_.bindAll(this,"success"),this.players=[],this.listenTo(this.controller,"close",wp.media.mixin.unsetPlayers),this.on("ready",this.setPlayer),this.on("media:setting:remove",wp.media.mixin.unsetPlayers,this),this.on("media:setting:remove",this.render),this.on("media:setting:remove",this.setPlayer),d.prototype.initialize.apply(this,arguments)},events:function(){return _.extend({"click .remove-setting":"removeSetting","change .content-track":"setTracks","click .remove-track":"setTracks","click .add-media-source":"addSource"},d.prototype.events)},prepare:function(){return _.defaults({model:this.model.toJSON()},this.options)},removeSetting:function(a){var b,c=e(a.currentTarget).parent();b=c.find("input").data("setting"),b&&(this.model.unset(b),this.trigger("media:setting:remove",this)),c.remove()},setTracks:function(){var a="";_.each(this.$(".content-track"),function(b){a+=e(b).val()}),this.model.set("content",a),this.trigger("media:setting:remove",this)},addSource:function(a){this.controller.lastMime=e(a.currentTarget).data("mime"),this.controller.setState("add-"+this.controller.defaults.id+"-source")},loadPlayer:function(){this.players.push(new MediaElementPlayer(this.media,this.settings)),this.scriptXhr=!1},setPlayer:function(){var a;this.players.length||!this.media||this.scriptXhr||(a=this.model.get("src"),a&&a.indexOf("vimeo")>-1&&!("Vimeo"in window)?this.scriptXhr=e.getScript("https://player.vimeo.com/api/player.js",_.bind(this.loadPlayer,this)):this.loadPlayer())},setMedia:function(){return this},success:function(a){var b=a.attributes.autoplay&&"false"!==a.attributes.autoplay;"flash"===a.pluginType&&b&&a.addEventListener("canplay",function(){a.play()},!1),this.mejs=a},render:function(){return d.prototype.render.apply(this,arguments),setTimeout(_.bind(function(){this.resetFocus()},this),10),this.settings=_.defaults({success:this.success},wp.media.mixin.mejsSettings),this.setMedia()},resetFocus:function(){this.$(".embed-media-settings").scrollTop(0)}},{instances:0,prepareSrc:function(a){var b=c.instances++;return _.each(e(a).find("source"),function(a){a.src=[a.src,a.src.indexOf("?")>-1?"&":"?","_=",b].join("")}),a}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaDetails;c=d.extend({className:"audio-details",template:wp.template("audio-details"),setMedia:function(){var a=this.$(".wp-audio-shortcode");return a.find("source").length?(a.is(":hidden")&&a.show(),this.media=d.prepareSrc(a.get(0))):(a.hide(),this.media=!1),this}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaDetails;c=d.extend({className:"video-details",template:wp.template("video-details"),setMedia:function(){var a=this.$(".wp-video-shortcode");return a.find("source").length?(a.is(":hidden")&&a.show(),a.hasClass("youtube-video")||a.hasClass("vimeo-video")?this.media=a.get(0):this.media=d.prepareSrc(a.get(0))):(a.hide(),this.media=!1),this}}),a.exports=c}]);
!function(a,b,c,d){"use strict";var e={},f={};b.mce=b.mce||{},b.mce.views={register:function(a,c){e[a]=b.mce.View.extend(_.extend(c,{type:a}))},unregister:function(a){delete e[a]},get:function(a){return e[a]},unbind:function(){_.each(f,function(a){a.unbind()})},setMarkers:function(a,b){var c,d,f=[{content:a}],g=this;return _.each(e,function(a,e){d=f.slice(),f=[],_.each(d,function(d){var h,i,j=d.content;if(d.processed)return void f.push(d);for(;j&&(h=a.prototype.match(j));)h.index&&f.push({content:j.substring(0,h.index)}),h.options.editor=b,c=g.createInstance(e,h.content,h.options),i=c.loader?".":c.text,f.push({content:c.ignore?i:'<p data-wpview-marker="'+c.encodedText+'">'+i+"</p>",processed:!0}),j=j.slice(h.index+h.content.length);j&&f.push({content:j})})}),a=_.pluck(f,"content").join(""),a.replace(/<p>\s*<p data-wpview-marker=/g,"<p data-wpview-marker=").replace(/<\/p>\s*<\/p>/g,"</p>")},createInstance:function(a,b,c,d){var e,g,h=this.get(a);return b.indexOf("[")!==-1&&b.indexOf("]")!==-1&&(b=b.replace(/\[[^\]]+\]/g,function(a){return a.replace(/[\r\n]/g,"")})),!d&&(g=this.getInstance(b))?g:(e=encodeURIComponent(b),c=_.extend(c||{},{text:b,encodedText:e}),f[e]=new h(c))},getInstance:function(a){return"string"==typeof a?f[encodeURIComponent(a)]:f[d(a).attr("data-wpview-text")]},getText:function(a){return decodeURIComponent(d(a).attr("data-wpview-text")||"")},render:function(a){_.each(f,function(b){b.render(null,a)})},update:function(a,b,c,d){var e=this.getInstance(c);e&&e.update(a,b,c,d)},edit:function(a,b){var c=this.getInstance(b);c&&c.edit&&c.edit(c.text,function(d,e){c.update(d,a,b,e)})},remove:function(a,b){var c=this.getInstance(b);c&&c.remove(a,b)}},b.mce.View=function(a){_.extend(this,a),this.initialize()},b.mce.View.extend=Backbone.View.extend,_.extend(b.mce.View.prototype,{content:null,loader:!0,initialize:function(){},getContent:function(){return this.content},render:function(a,b){null!=a&&(this.content=a),a=this.getContent(),(this.loader||a)&&(b&&this.unbind(),this.replaceMarkers(),a?this.setContent(a,function(a,b){d(b).data("rendered",!0),this.bindNode.call(this,a,b)},!!b&&null):this.setLoader())},bindNode:function(){},unbindNode:function(){},unbind:function(){this.getNodes(function(a,b){this.unbindNode.call(this,a,b)},!0)},getEditors:function(a){_.each(tinymce.editors,function(b){b.plugins.wpview&&a.call(this,b)},this)},getNodes:function(a,b){this.getEditors(function(c){var e=this;d(c.getBody()).find('[data-wpview-text="'+e.encodedText+'"]').filter(function(){var a;return null==b||(a=d(this).data("rendered")===!0,b?a:!a)}).each(function(){a.call(e,c,this,this)})})},getMarkers:function(a){this.getEditors(function(b){var c=this;d(b.getBody()).find('[data-wpview-marker="'+this.encodedText+'"]').each(function(){a.call(c,b,this)})})},replaceMarkers:function(){this.getMarkers(function(a,b){var c,e=b===a.selection.getNode();return this.loader||d(b).text()===tinymce.DOM.decode(this.text)?(c=a.$('<div class="wpview wpview-wrap" data-wpview-text="'+this.encodedText+'" data-wpview-type="'+this.type+'" contenteditable="false"></div>'),a.$(b).replaceWith(c),void(e&&setTimeout(function(){a.selection.select(c[0]),a.selection.collapse()}))):void a.dom.setAttrib(b,"data-wpview-marker",null)})},removeMarkers:function(){this.getMarkers(function(a,b){a.dom.setAttrib(b,"data-wpview-marker",null)})},setContent:function(a,b,c){_.isObject(a)&&(a.sandbox||a.head||a.body.indexOf("<script")!==-1)?this.setIframes(a.head||"",a.body,b,c):_.isString(a)&&a.indexOf("<script")!==-1?this.setIframes("",a,b,c):this.getNodes(function(c,d){a=a.body||a,a.indexOf("<iframe")!==-1&&(a+='<span class="mce-shim"></span>'),c.undoManager.transact(function(){d.innerHTML="",d.appendChild(_.isString(a)?c.dom.createFragment(a):a),c.dom.add(d,"span",{"class":"wpview-end"})}),b&&b.call(this,c,d)},c)},setIframes:function(c,e,f,g){var h=this;if(e.indexOf("[")!==-1&&e.indexOf("]")!==-1){var i=new RegExp("\\[\\/?(?:"+a.mceViewL10n.shortcodes.join("|")+")[^\\]]*?\\]","g");e=e.replace(i,function(a){return a.replace(/</g,"&lt;").replace(/>/g,"&gt;")})}this.getNodes(function(a,g){function i(){var b;r||l.contentWindow&&(b=d(l),h.iframeHeight=d(n.body).height(),b.height()!==h.iframeHeight&&(b.height(h.iframeHeight),a.nodeChanged()))}function j(){a.isHidden()||(d(g).data("rendered",null),setTimeout(function(){b.mce.views.render()}))}function k(){p=new o(_.debounce(i,100)),p.observe(n.body,{attributes:!0,childList:!0,subtree:!0})}var l,m,n,o,p,q,r,s=a.dom,t="",u=a.getBody().className||"",v=a.getDoc().getElementsByTagName("head")[0];if(tinymce.each(s.$('link[rel="stylesheet"]',v),function(a){a.href&&a.href.indexOf("skins/lightgray/content.min.css")===-1&&a.href.indexOf("skins/wordpress/wp-content.css")===-1&&(t+=s.getOuterHTML(a))}),h.iframeHeight&&s.add(g,"span",{"data-mce-bogus":1,style:{display:"block",width:"100%",height:h.iframeHeight}},"\u200b"),a.undoManager.transact(function(){g.innerHTML="",l=s.add(g,"iframe",{src:tinymce.Env.ie?'javascript:""':"",frameBorder:"0",allowTransparency:"true",scrolling:"no","class":"wpview-sandbox",style:{width:"100%",display:"block"},height:h.iframeHeight}),s.add(g,"span",{"class":"mce-shim"}),s.add(g,"span",{"class":"wpview-end"})}),l.contentWindow){if(m=l.contentWindow,n=m.document,n.open(),n.write('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'+c+t+'<style>html {background: transparent;padding: 0;margin: 0;}body#wpview-iframe-sandbox {background: transparent;padding: 1px 0 !important;margin: -1px 0 0 !important;}body#wpview-iframe-sandbox:before,body#wpview-iframe-sandbox:after {display: none;content: "";}iframe {max-width: 100%;}</style></head><body id="wpview-iframe-sandbox" class="'+u+'">'+e+"</body></html>"),n.close(),h.iframeHeight&&(r=!0,setTimeout(function(){r=!1,i()},3e3)),d(m).on("load",i).on("unload",j),o=m.MutationObserver||m.WebKitMutationObserver||m.MozMutationObserver)n.body?k():n.addEventListener("DOMContentLoaded",k,!1);else for(q=1;q<6;q++)setTimeout(i,700*q);f&&f.call(h,a,g)}},g)},setLoader:function(a){this.setContent('<div class="loading-placeholder"><div class="dashicons dashicons-'+(a||"admin-media")+'"></div><div class="wpview-loading"><ins></ins></div></div>')},setError:function(a,b){this.setContent('<div class="wpview-error"><div class="dashicons dashicons-'+(b||"no")+'"></div><p>'+a+"</p></div>")},match:function(a){var b=c.next(this.type,a);if(b)return{index:b.index,content:b.content,options:{shortcode:b.shortcode}}},update:function(a,c,f,g){_.find(e,function(e,h){var i=e.prototype.match(a);if(i)return d(f).data("rendered",!1),c.dom.setAttrib(f,"data-wpview-text",encodeURIComponent(a)),b.mce.views.createInstance(h,a,i.options,g).render(),c.selection.select(f),c.nodeChanged(),c.focus(),!0})},remove:function(a,b){this.unbindNode.call(this,a,b),a.dom.remove(b),a.focus()}})}(window,window.wp,window.wp.shortcode,window.jQuery),function(a,b,c,d){function e(b){var c={};return a.tinymce?!b||b.indexOf("<")===-1&&b.indexOf(">")===-1?b:(j=j||new a.tinymce.html.Schema(c),k=k||new a.tinymce.html.DomParser(c,j),l=l||new a.tinymce.html.Serializer(c,j),l.serialize(k.parse(b,{forced_root_block:!1}))):b.replace(/<[^>]+>/g,"")}var f,g,h,i,j,k,l;f={state:[],edit:function(a,b){var d=this.type,e=c[d].edit(a);this.pausePlayers&&this.pausePlayers(),_.each(this.state,function(a){e.state(a).on("update",function(a){b(c[d].shortcode(a).string(),"gallery"===d)})}),e.on("close",function(){e.detach()}),e.open()}},g=_.extend({},f,{state:["gallery-edit"],template:c.template("editor-gallery"),initialize:function(){var a=c.gallery.attachments(this.shortcode,c.view.settings.post.id),b=this.shortcode.attrs.named,d=this;a.more().done(function(){a=a.toJSON(),_.each(a,function(a){a.sizes&&(b.size&&a.sizes[b.size]?a.thumbnail=a.sizes[b.size]:a.sizes.thumbnail?a.thumbnail=a.sizes.thumbnail:a.sizes.full&&(a.thumbnail=a.sizes.full))}),d.render(d.template({verifyHTML:e,attachments:a,columns:b.columns?parseInt(b.columns,10):c.galleryDefaults.columns}))}).fail(function(a,b){d.setError(b)})}}),h=_.extend({},f,{action:"parse-media-shortcode",initialize:function(){var a=this,b=null;this.url&&(this.loader=!1,this.shortcode=c.embed.shortcode({url:this.text})),a.editor&&(b=a.editor.getBody().clientWidth),wp.ajax.post(this.action,{post_ID:c.view.settings.post.id,type:this.shortcode.tag,shortcode:this.shortcode.string(),maxwidth:b}).done(function(b){a.render(b)}).fail(function(b){a.url?(a.ignore=!0,a.removeMarkers()):a.setError(b.message||b.statusText,"admin-media")}),this.getEditors(function(b){b.on("wpview-selected",function(){a.pausePlayers()})})},pausePlayers:function(){this.getNodes(function(a,b,c){var e=d("iframe.wpview-sandbox",c).get(0);e&&(e=e.contentWindow)&&e.mejs&&_.each(e.mejs.players,function(a){try{a.pause()}catch(b){}})})}}),i=_.extend({},h,{action:"parse-embed",edit:function(a,b){var d=c.embed.edit(a,this.url),e=this;this.pausePlayers(),d.state("embed").props.on("change:url",function(a,b){b&&a.get("url")&&(d.state("embed").metadata=a.toJSON())}),d.state("embed").on("select",function(){var a=d.state("embed").metadata;b(e.url?a.url:c.embed.shortcode(a).string())}),d.on("close",function(){d.detach()}),d.open()}}),b.register("gallery",_.extend({},g)),b.register("audio",_.extend({},h,{state:["audio-details"]})),b.register("video",_.extend({},h,{state:["video-details"]})),b.register("playlist",_.extend({},h,{state:["playlist-edit","video-playlist-edit"]})),b.register("embed",_.extend({},i)),b.register("embedURL",_.extend({},i,{match:function(a){var b=/(^|<p>)(https?:\/\/[^\s"]+?)(<\/p>\s*|$)/gi,c=b.exec(a);if(c)return{index:c.index+c[1].length,content:c[2],options:{url:!0}}}}))}(window,window.wp.mce.views,window.wp.media,window.jQuery);
!function(a){function b(){return a("<div/>")}var c=Math.abs,d=Math.max,e=Math.min,f=Math.round;a.imgAreaSelect=function(g,h){function i(a){return a+ra.left-sa.left}function j(a){return a+ra.top-sa.top}function k(a){return a-ra.left+sa.left}function l(a){return a-ra.top+sa.top}function m(a){return d(a.pageX||0,o(a).x)-sa.left}function n(a){return d(a.pageY||0,o(a).y)-sa.top}function o(a){var b=a.originalEvent||{};return b.touches&&b.touches.length?{x:b.touches[0].pageX,y:b.touches[0].pageY}:{x:0,y:0}}function p(a){var b=a||T,c=a||U;return{x1:f(va.x1*b),y1:f(va.y1*c),x2:f(va.x2*b),y2:f(va.y2*c),width:f(va.x2*b)-f(va.x1*b),height:f(va.y2*c)-f(va.y1*c)}}function q(a,b,c,d,e){var g=e||T,h=e||U;va={x1:f(a/g||0),y1:f(b/h||0),x2:f(c/g||0),y2:f(d/h||0)},va.width=va.x2-va.x1,va.height=va.y2-va.y1}function r(){K&&la.width()&&(ra={left:f(la.offset().left),top:f(la.offset().top)},O=la.innerWidth(),P=la.innerHeight(),ra.top+=la.outerHeight()-P>>1,ra.left+=la.outerWidth()-O>>1,W=f(h.minWidth/T)||0,X=f(h.minHeight/U)||0,Y=f(e(h.maxWidth/T||1<<24,O)),Z=f(e(h.maxHeight/U||1<<24,P)),"1.3.2"!=a().jquery||"fixed"!=ua||wa.getBoundingClientRect||(ra.top+=d(document.body.scrollTop,wa.scrollTop),ra.left+=d(document.body.scrollLeft,wa.scrollLeft)),sa=/absolute|relative/.test(Q.css("position"))?{left:f(Q.offset().left)-Q.scrollLeft(),top:f(Q.offset().top)-Q.scrollTop()}:"fixed"==ua?{left:a(document).scrollLeft(),top:a(document).scrollTop()}:{left:0,top:0},M=i(0),N=j(0),(va.x2>O||va.y2>P)&&z())}function s(b){if(_){switch(ma.css({left:i(va.x1),top:j(va.y1)}).add(na).width(ia=va.width).height(ja=va.height),na.add(oa).add(qa).css({left:0,top:0}),oa.width(d(ia-oa.outerWidth()+oa.innerWidth(),0)).height(d(ja-oa.outerHeight()+oa.innerHeight(),0)),a(pa[0]).css({left:M,top:N,width:va.x1,height:P}),a(pa[1]).css({left:M+va.x1,top:N,width:ia,height:va.y1}),a(pa[2]).css({left:M+va.x2,top:N,width:O-va.x2,height:P}),a(pa[3]).css({left:M+va.x1,top:N+va.y2,width:ia,height:P-va.y2}),ia-=qa.outerWidth(),ja-=qa.outerHeight(),qa.length){case 8:a(qa[4]).css({left:ia>>1}),a(qa[5]).css({left:ia,top:ja>>1}),a(qa[6]).css({left:ia>>1,top:ja}),a(qa[7]).css({top:ja>>1});case 4:qa.slice(1,3).css({left:ia}),qa.slice(2,4).css({top:ja})}b!==!1&&(a.imgAreaSelect.onKeyPress!=ya&&a(document).unbind(a.imgAreaSelect.keyPress,a.imgAreaSelect.onKeyPress),h.keys&&a(document)[a.imgAreaSelect.keyPress](a.imgAreaSelect.onKeyPress=ya)),za&&oa.outerWidth()-oa.innerWidth()==2&&(oa.css("margin",0),setTimeout(function(){oa.css("margin","auto")},0))}}function t(a){r(),s(a),aa=i(va.x1),ba=j(va.y1),ca=i(va.x2),da=j(va.y2)}function u(a,b){h.fadeSpeed?a.fadeOut(h.fadeSpeed,b):a.hide()}function v(a){var b=k(m(a))-va.x1,c=l(n(a))-va.y1;ka||(r(),ka=!0,ma.one("mouseout",function(){ka=!1})),V="",h.resizable&&(c<=h.resizeMargin?V="n":c>=va.height-h.resizeMargin&&(V="s"),b<=h.resizeMargin?V+="w":b>=va.width-h.resizeMargin&&(V+="e")),ma.css("cursor",V?V+"-resize":h.movable?"move":""),L&&L.toggle()}function w(b){a("body").css("cursor",""),(h.autoHide||va.width*va.height==0)&&u(ma.add(pa),function(){a(this).hide()}),a(document).off("mousemove touchmove",A),ma.on("mousemove touchmove",v),h.onSelectEnd(g,p())}function x(b){return("mousedown"!=b.type||1==b.which)&&(v(b),r(),V?(a("body").css("cursor",V+"-resize"),aa=i(va[/w/.test(V)?"x2":"x1"]),ba=j(va[/n/.test(V)?"y2":"y1"]),a(document).on("mousemove touchmove",A).one("mouseup touchend",w),ma.off("mousemove touchmove",v)):h.movable?(R=M+va.x1-m(b),S=N+va.y1-n(b),ma.off("mousemove touchmove",v),a(document).on("mousemove touchmove",C).one("mouseup touchend",function(){h.onSelectEnd(g,p()),a(document).off("mousemove touchmove",C),ma.on("mousemove touchmove",v)})):la.mousedown(b),!1)}function y(a){$&&(a?(ca=d(M,e(M+O,aa+c(da-ba)*$*(ca>aa||-1))),da=f(d(N,e(N+P,ba+c(ca-aa)/$*(da>ba||-1)))),ca=f(ca)):(da=d(N,e(N+P,ba+c(ca-aa)/$*(da>ba||-1))),ca=f(d(M,e(M+O,aa+c(da-ba)*$*(ca>aa||-1)))),da=f(da)))}function z(){aa=e(aa,M+O),ba=e(ba,N+P),c(ca-aa)<W&&(ca=aa-W*(ca<aa||-1),ca<M?aa=M+W:ca>M+O&&(aa=M+O-W)),c(da-ba)<X&&(da=ba-X*(da<ba||-1),da<N?ba=N+X:da>N+P&&(ba=N+P-X)),ca=d(M,e(ca,M+O)),da=d(N,e(da,N+P)),y(c(ca-aa)<c(da-ba)*$),c(ca-aa)>Y&&(ca=aa-Y*(ca<aa||-1),y()),c(da-ba)>Z&&(da=ba-Z*(da<ba||-1),y(!0)),va={x1:k(e(aa,ca)),x2:k(d(aa,ca)),y1:l(e(ba,da)),y2:l(d(ba,da)),width:c(ca-aa),height:c(da-ba)},s(),h.onSelectChange(g,p())}function A(a){return ca=/w|e|^$/.test(V)||$?m(a):i(va.x2),da=/n|s|^$/.test(V)||$?n(a):j(va.y2),z(),!1}function B(b,c){ca=(aa=b)+va.width,da=(ba=c)+va.height,a.extend(va,{x1:k(aa),y1:l(ba),x2:k(ca),y2:l(da)}),s(),h.onSelectChange(g,p())}function C(a){return aa=d(M,e(R+m(a),M+O-va.width)),ba=d(N,e(S+n(a),N+P-va.height)),B(aa,ba),a.preventDefault(),!1}function D(){a(document).off("mousemove touchmove",D),r(),ca=aa,da=ba,z(),V="",pa.is(":visible")||ma.add(pa).hide().fadeIn(h.fadeSpeed||0),_=!0,a(document).off("mouseup touchend",E).on("mousemove touchmove",A).one("mouseup touchend",w),ma.off("mousemove touchmove",v),h.onSelectStart(g,p())}function E(){a(document).off("mousemove touchmove",D).off("mouseup touchend",E),u(ma.add(pa)),q(k(aa),l(ba),k(aa),l(ba)),this instanceof a.imgAreaSelect||(h.onSelectChange(g,p()),h.onSelectEnd(g,p()))}function F(b){return!(b.which>1||pa.is(":animated"))&&(r(),R=aa=m(b),S=ba=n(b),a(document).on({"mousemove touchmove":D,"mouseup touchend":E}),!1)}function G(){t(!1)}function H(){K=!0,J(h=a.extend({classPrefix:"imgareaselect",movable:!0,parent:"body",resizable:!0,resizeMargin:10,onInit:function(){},onSelectStart:function(){},onSelectChange:function(){},onSelectEnd:function(){}},h)),ma.add(pa).css({visibility:""}),h.show&&(_=!0,r(),s(),ma.add(pa).hide().fadeIn(h.fadeSpeed||0)),setTimeout(function(){h.onInit(g,p())},0)}function I(a,b){for(var c in b)void 0!==h[c]&&a.css(b[c],h[c])}function J(c){if(c.parent&&(Q=a(c.parent)).append(ma.add(pa)),a.extend(h,c),r(),null!=c.handles){for(qa.remove(),qa=a([]),ga=c.handles?"corners"==c.handles?4:8:0;ga--;)qa=qa.add(b());qa.addClass(h.classPrefix+"-handle").css({position:"absolute",fontSize:0,zIndex:ta+1||1}),!parseInt(qa.css("width"))>=0&&qa.width(5).height(5),(ha=h.borderWidth)&&qa.css({borderWidth:ha,borderStyle:"solid"}),I(qa,{borderColor1:"border-color",borderColor2:"background-color",borderOpacity:"opacity"})}for(T=h.imageWidth/O||1,U=h.imageHeight/P||1,null!=c.x1&&(q(c.x1,c.y1,c.x2,c.y2),c.show=!c.hide),c.keys&&(h.keys=a.extend({shift:1,ctrl:"resize"},c.keys)),pa.addClass(h.classPrefix+"-outer"),na.addClass(h.classPrefix+"-selection"),ga=0;ga++<4;)a(oa[ga-1]).addClass(h.classPrefix+"-border"+ga);I(na,{selectionColor:"background-color",selectionOpacity:"opacity"}),I(oa,{borderOpacity:"opacity",borderWidth:"border-width"}),I(pa,{outerColor:"background-color",outerOpacity:"opacity"}),(ha=h.borderColor1)&&a(oa[0]).css({borderStyle:"solid",borderColor:ha}),(ha=h.borderColor2)&&a(oa[1]).css({borderStyle:"dashed",borderColor:ha}),ma.append(na.add(oa).add(L)).append(qa),za&&((ha=(pa.css("filter")||"").match(/opacity=(\d+)/))&&pa.css("opacity",ha[1]/100),(ha=(oa.css("filter")||"").match(/opacity=(\d+)/))&&oa.css("opacity",ha[1]/100)),c.hide?u(ma.add(pa)):c.show&&K&&(_=!0,ma.add(pa).fadeIn(h.fadeSpeed||0),t()),$=(fa=(h.aspectRatio||"").split(/:/))[0]/fa[1],la.add(pa).unbind("mousedown",F),h.disable||h.enable===!1?(ma.off({"mousemove touchmove":v,"mousedown touchstart":x}),a(window).off("resize",G)):((h.enable||h.disable===!1)&&((h.resizable||h.movable)&&ma.on({"mousemove touchmove":v,"mousedown touchstart":x}),a(window).resize(G)),h.persistent||la.add(pa).on("mousedown touchstart",F)),h.enable=h.disable=void 0}var K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,$,_,aa,ba,ca,da,ea,fa,ga,ha,ia,ja,ka,la=a(g),ma=b(),na=b(),oa=b().add(b()).add(b()).add(b()),pa=b().add(b()).add(b()).add(b()),qa=a([]),ra={left:0,top:0},sa={left:0,top:0},ta=0,ua="absolute",va={x1:0,y1:0,x2:0,y2:0,width:0,height:0},wa=document.documentElement,xa=navigator.userAgent,ya=function(a){var b,c,f=h.keys,g=a.keyCode;if(b=isNaN(f.alt)||!a.altKey&&!a.originalEvent.altKey?!isNaN(f.ctrl)&&a.ctrlKey?f.ctrl:!isNaN(f.shift)&&a.shiftKey?f.shift:isNaN(f.arrows)?10:f.arrows:f.alt,"resize"==f.arrows||"resize"==f.shift&&a.shiftKey||"resize"==f.ctrl&&a.ctrlKey||"resize"==f.alt&&(a.altKey||a.originalEvent.altKey)){switch(g){case 37:b=-b;case 39:c=d(aa,ca),aa=e(aa,ca),ca=d(c+b,aa),y();break;case 38:b=-b;case 40:c=d(ba,da),ba=e(ba,da),da=d(c+b,ba),y(!0);break;default:return}z()}else switch(aa=e(aa,ca),ba=e(ba,da),g){case 37:B(d(aa-b,M),ba);break;case 38:B(aa,d(ba-b,N));break;case 39:B(aa+e(b,O-k(ca)),ba);break;case 40:B(aa,ba+e(b,P-l(da)));break;default:return}return!1};this.remove=function(){J({disable:!0}),ma.add(pa).remove()},this.getOptions=function(){return h},this.setOptions=J,this.getSelection=p,this.setSelection=q,this.cancelSelection=E,this.update=t;var za=(/msie ([\w.]+)/i.exec(xa)||[])[1],Aa=/opera/i.test(xa),Ba=/webkit/i.test(xa)&&!/chrome/i.test(xa);for(ea=la;ea.length;)ta=d(ta,isNaN(ea.css("z-index"))?ta:ea.css("z-index")),"fixed"==ea.css("position")&&(ua="fixed"),ea=ea.parent(":not(body)");ta=h.zIndex||ta,za&&la.attr("unselectable","on"),a.imgAreaSelect.keyPress=za||Ba?"keydown":"keypress",Aa&&(L=b().css({width:"100%",height:"100%",position:"absolute",zIndex:ta+2||2})),ma.add(pa).css({visibility:"hidden",position:ua,overflow:"hidden",zIndex:ta||"0"}),ma.css({zIndex:ta+2||2}),na.add(oa).css({position:"absolute",fontSize:0}),g.complete||"complete"==g.readyState||!la.is("img")?H():la.one("load",H),!K&&za&&za>=7&&(g.src=g.src)},a.fn.imgAreaSelect=function(b){return b=b||{},this.each(function(){a(this).data("imgAreaSelect")?b.remove?(a(this).data("imgAreaSelect").remove(),a(this).removeData("imgAreaSelect")):a(this).data("imgAreaSelect").setOptions(b):b.remove||(void 0===b.enable&&void 0===b.disable&&(b.enable=!0),a(this).data("imgAreaSelect",new a.imgAreaSelect(this,b)))}),b.instance?a(this).data("imgAreaSelect"):this}}(jQuery);
!function(a){var b=window.imageEdit={iasapi:{},hold:{},postid:"",_view:!1,intval:function(a){return 0|a},setDisabled:function(a,b){b?a.removeClass("disabled").prop("disabled",!1):a.addClass("disabled").prop("disabled",!0)},init:function(b){var c=this,d=a("#image-editor-"+c.postid),e=c.intval(a("#imgedit-x-"+b).val()),f=c.intval(a("#imgedit-y-"+b).val());c.postid!==b&&d.length&&c.close(c.postid),c.hold.w=c.hold.ow=e,c.hold.h=c.hold.oh=f,c.hold.xy_ratio=e/f,c.hold.sizer=parseFloat(a("#imgedit-sizer-"+b).val()),c.postid=b,a("#imgedit-response-"+b).empty(),a('input[type="text"]',"#imgedit-panel-"+b).keypress(function(b){var c=b.keyCode;if(36<c&&c<41&&a(this).blur(),13===c)return b.preventDefault(),b.stopPropagation(),!1})},toggleEditor:function(b,c){var d=a("#imgedit-wait-"+b);c?d.fadeIn("fast"):d.fadeOut("fast")},toggleHelp:function(b){var c=a(b);return c.attr("aria-expanded","false"===c.attr("aria-expanded")?"true":"false").parents(".imgedit-group-top").toggleClass("imgedit-help-toggled").find(".imgedit-help").slideToggle("fast"),!1},getTarget:function(b){return a('input[name="imgedit-target-'+b+'"]:checked',"#imgedit-save-target-"+b).val()||"full"},scaleChanged:function(b,c,d){var e=a("#imgedit-scale-width-"+b),f=a("#imgedit-scale-height-"+b),g=a("#imgedit-scale-warn-"+b),h="",i="";!1!==this.validateNumeric(d)&&(c?(i=""!==e.val()?Math.round(e.val()/this.hold.xy_ratio):"",f.val(i)):(h=""!==f.val()?Math.round(f.val()*this.hold.xy_ratio):"",e.val(h)),i&&i>this.hold.oh||h&&h>this.hold.ow?g.css("visibility","visible"):g.css("visibility","hidden"))},getSelRatio:function(b){var c=this.hold.w,d=this.hold.h,e=this.intval(a("#imgedit-crop-width-"+b).val()),f=this.intval(a("#imgedit-crop-height-"+b).val());return e&&f?e+":"+f:c&&d?c+":"+d:"1:1"},filterHistory:function(b,c){var d,e,f,g,h=a("#imgedit-history-"+b).val(),i=[];if(""!==h){if(h=JSON.parse(h),d=this.intval(a("#imgedit-undone-"+b).val()),d>0)for(;d>0;)h.pop(),d--;if(c){if(!h.length)return this.hold.w=this.hold.ow,this.hold.h=this.hold.oh,"";f=h[h.length-1],f=f.c||f.r||f.f||!1,f&&(this.hold.w=f.fw,this.hold.h=f.fh)}for(e in h)g=h[e],g.hasOwnProperty("c")?i[e]={c:{x:g.c.x,y:g.c.y,w:g.c.w,h:g.c.h}}:g.hasOwnProperty("r")?i[e]={r:g.r.r}:g.hasOwnProperty("f")&&(i[e]={f:g.f.f});return JSON.stringify(i)}return""},refreshEditor:function(c,d,e){var f,g,h=this;h.toggleEditor(c,1),f={action:"imgedit-preview",_ajax_nonce:d,postid:c,history:h.filterHistory(c,1),rand:h.intval(1e6*Math.random())},g=a('<img id="image-preview-'+c+'" alt="" />').on("load",{history:f.history},function(d){var f,h,i,j=a("#imgedit-crop-"+c),k=b;""!==d.data.history&&(i=JSON.parse(d.data.history),i[i.length-1].hasOwnProperty("c")&&(k.setDisabled(a("#image-undo-"+c),!0),a("#image-undo-"+c).focus())),j.empty().append(g),f=Math.max(k.hold.w,k.hold.h),h=Math.max(a(g).width(),a(g).height()),k.hold.sizer=f>h?h/f:1,k.initCrop(c,g,j),k.setCropSelection(c,0),"undefined"!=typeof e&&null!==e&&e(),a("#imgedit-history-"+c).val()&&"0"===a("#imgedit-undone-"+c).val()?a("input.imgedit-submit-btn","#imgedit-panel-"+c).removeAttr("disabled"):a("input.imgedit-submit-btn","#imgedit-panel-"+c).prop("disabled",!0),k.toggleEditor(c,0)}).on("error",function(){a("#imgedit-crop-"+c).empty().append('<div class="error"><p>'+imageEditL10n.error+"</p></div>"),h.toggleEditor(c,0)}).attr("src",ajaxurl+"?"+a.param(f))},action:function(b,c,d){var e,f,g,h,i,j=this;if(j.notsaved(b))return!1;if(e={action:"image-editor",_ajax_nonce:c,postid:b},"scale"===d){if(f=a("#imgedit-scale-width-"+b),g=a("#imgedit-scale-height-"+b),h=j.intval(f.val()),i=j.intval(g.val()),h<1)return f.focus(),!1;if(i<1)return g.focus(),!1;if(h===j.hold.ow||i===j.hold.oh)return!1;e["do"]="scale",e.fwidth=h,e.fheight=i}else{if("restore"!==d)return!1;e["do"]="restore"}j.toggleEditor(b,1),a.post(ajaxurl,e,function(c){a("#image-editor-"+b).empty().append(c),j.toggleEditor(b,0),j._view&&j._view.refresh()})},save:function(c,d){var e,f=this.getTarget(c),g=this.filterHistory(c,0),h=this;return""!==g&&(this.toggleEditor(c,1),e={action:"image-editor",_ajax_nonce:d,postid:c,history:g,target:f,context:a("#image-edit-context").length?a("#image-edit-context").val():null,"do":"save"},void a.post(ajaxurl,e,function(d){var e=JSON.parse(d);return e.error?(a("#imgedit-response-"+c).html('<div class="error"><p>'+e.error+"</p></div>"),void b.close(c)):(e.fw&&e.fh&&a("#media-dims-"+c).html(e.fw+" &times; "+e.fh),e.thumbnail&&a(".thumbnail","#thumbnail-head-"+c).attr("src",""+e.thumbnail),e.msg&&a("#imgedit-response-"+c).html('<div class="updated"><p>'+e.msg+"</p></div>"),void(h._view?h._view.save():b.close(c)))}))},open:function(c,d,e){this._view=e;var f,g,h=a("#image-editor-"+c),i=a("#media-head-"+c),j=a("#imgedit-open-btn-"+c),k=j.siblings(".spinner");if(!j.hasClass("button-activated"))return k.addClass("is-active"),g={action:"image-editor",_ajax_nonce:d,postid:c,"do":"open"},f=a.ajax({url:ajaxurl,type:"post",data:g,beforeSend:function(){j.addClass("button-activated")}}).done(function(a){h.html(a),i.fadeOut("fast",function(){h.fadeIn("fast"),j.removeClass("button-activated"),k.removeClass("is-active")}),b.init(c)})},imgLoaded:function(b){var c=a("#image-preview-"+b),d=a("#imgedit-crop-"+b);"undefined"==typeof this.hold.sizer&&this.init(b),this.initCrop(b,c,d),this.setCropSelection(b,0),this.toggleEditor(b,0),a(".imgedit-wrap .imgedit-help-toggle").eq(0).focus()},initCrop:function(c,d,e){var f,g=this,h=a("#imgedit-sel-width-"+c),i=a("#imgedit-sel-height-"+c);g.iasapi=a(d).imgAreaSelect({parent:e,instance:!0,handles:!0,keys:!0,minWidth:3,minHeight:3,onInit:function(b){f=a(b),f.next().css("position","absolute").nextAll(".imgareaselect-outer").css("position","absolute"),e.children().on("mousedown, touchstart",function(a){var b,d,e=!1;a.shiftKey&&(b=g.iasapi.getSelection(),d=g.getSelRatio(c),e=b&&b.width&&b.height?b.width+":"+b.height:d),g.iasapi.setOptions({aspectRatio:e})})},onSelectStart:function(){b.setDisabled(a("#imgedit-crop-sel-"+c),1)},onSelectEnd:function(a,d){b.setCropSelection(c,d)},onSelectChange:function(a,c){var d=b.hold.sizer;h.val(b.round(c.width/d)),i.val(b.round(c.height/d))}})},setCropSelection:function(b,c){var d;return c=c||0,!c||c.width<3&&c.height<3?(this.setDisabled(a(".imgedit-crop","#imgedit-panel-"+b),0),this.setDisabled(a("#imgedit-crop-sel-"+b),0),a("#imgedit-sel-width-"+b).val(""),a("#imgedit-sel-height-"+b).val(""),a("#imgedit-selection-"+b).val(""),!1):(d={x:c.x1,y:c.y1,w:c.width,h:c.height},this.setDisabled(a(".imgedit-crop","#imgedit-panel-"+b),1),void a("#imgedit-selection-"+b).val(JSON.stringify(d)))},close:function(b,c){return c=c||!1,(!c||!this.notsaved(b))&&(this.iasapi={},this.hold={},void(this._view?this._view.back():a("#image-editor-"+b).fadeOut("fast",function(){a("#media-head-"+b).fadeIn("fast",function(){a("#imgedit-open-btn-"+b).focus()}),a(this).empty()})))},notsaved:function(b){var c=a("#imgedit-history-"+b).val(),d=""!==c?JSON.parse(c):[],e=this.intval(a("#imgedit-undone-"+b).val());return e<d.length&&!confirm(a("#imgedit-leaving-"+b).html())},addStep:function(b,c,d){for(var e=this,f=a("#imgedit-history-"+c),g=""!==f.val()?JSON.parse(f.val()):[],h=a("#imgedit-undone-"+c),i=e.intval(h.val());i>0;)g.pop(),i--;h.val(0),g.push(b),f.val(JSON.stringify(g)),e.refreshEditor(c,d,function(){e.setDisabled(a("#image-undo-"+c),!0),e.setDisabled(a("#image-redo-"+c),!1)})},rotate:function(b,c,d,e){return!a(e).hasClass("disabled")&&void this.addStep({r:{r:b,fw:this.hold.h,fh:this.hold.w}},c,d)},flip:function(b,c,d,e){return!a(e).hasClass("disabled")&&void this.addStep({f:{f:b,fw:this.hold.w,fh:this.hold.h}},c,d)},crop:function(b,c,d){var e=a("#imgedit-selection-"+b).val(),f=this.intval(a("#imgedit-sel-width-"+b).val()),g=this.intval(a("#imgedit-sel-height-"+b).val());return!a(d).hasClass("disabled")&&""!==e&&(e=JSON.parse(e),void(e.w>0&&e.h>0&&f>0&&g>0&&(e.fw=f,e.fh=g,this.addStep({c:e},b,c))))},undo:function(b,c){var d=this,e=a("#image-undo-"+b),f=a("#imgedit-undone-"+b),g=d.intval(f.val())+1;e.hasClass("disabled")||(f.val(g),d.refreshEditor(b,c,function(){var c=a("#imgedit-history-"+b),f=""!==c.val()?JSON.parse(c.val()):[];d.setDisabled(a("#image-redo-"+b),!0),d.setDisabled(e,g<f.length),f.length===g&&a("#image-redo-"+b).focus()}))},redo:function(b,c){var d=this,e=a("#image-redo-"+b),f=a("#imgedit-undone-"+b),g=d.intval(f.val())-1;e.hasClass("disabled")||(f.val(g),d.refreshEditor(b,c,function(){d.setDisabled(a("#image-undo-"+b),!0),d.setDisabled(e,g>0),0===g&&a("#image-undo-"+b).focus()}))},setNumSelection:function(b,c){var d,e,f,g,h,i=a("#imgedit-sel-width-"+b),j=a("#imgedit-sel-height-"+b),k=this.intval(i.val()),l=this.intval(j.val()),m=a("#image-preview-"+b),n=m.height(),o=m.width(),p=this.hold.sizer,q=this.iasapi;if(!1!==this.validateNumeric(c))return k<1?(i.val(""),!1):l<1?(j.val(""),!1):void(k&&l&&(d=q.getSelection())&&(g=d.x1+Math.round(k*p),h=d.y1+Math.round(l*p),e=d.x1,f=d.y1,g>o&&(e=0,g=o,i.val(Math.round(g/p))),h>n&&(f=0,h=n,j.val(Math.round(h/p))),q.setSelection(e,f,g,h),q.update(),this.setCropSelection(b,q.getSelection())))},round:function(a){var b;return a=Math.round(a),this.hold.sizer>.6?a:(b=a.toString().slice(-1),"1"===b?a-1:"9"===b?a+1:a)},setRatioSelection:function(b,c,d){var e,f,g=this.intval(a("#imgedit-crop-width-"+b).val()),h=this.intval(a("#imgedit-crop-height-"+b).val()),i=a("#image-preview-"+b).height();!1!==this.validateNumeric(d)&&g&&h&&(this.iasapi.setOptions({aspectRatio:g+":"+h}),(e=this.iasapi.getSelection(!0))&&(f=Math.ceil(e.y1+(e.x2-e.x1)/(g/h)),f>i&&(f=i,c?a("#imgedit-crop-height-"+b).val(""):a("#imgedit-crop-width-"+b).val("")),this.iasapi.setSelection(e.x1,e.y1,e.x2,f),this.iasapi.update()))},validateNumeric:function(b){if(!this.intval(a(b).val()))return a(b).val(""),!1}}}(jQuery);
!function(a){function b(d){if(c[d])return c[d].exports;var e=c[d]={i:d,l:!1,exports:{}};return a[d].call(e.exports,e,e.exports,b),e.l=!0,e.exports}var c={};return b.m=a,b.c=c,b.d=function(a,c,d){b.o(a,c)||Object.defineProperty(a,c,{configurable:!1,enumerable:!0,get:d})},b.n=function(a){var c=a&&a.__esModule?function(){return a["default"]}:function(){return a};return b.d(c,"a",c),c},b.o=function(a,b){return Object.prototype.hasOwnProperty.call(a,b)},b.p="",b(b.s=10)}([,,,,,,,,,,function(a,b,c){var d=wp.media;d.controller.EditAttachmentMetadata=c(11),d.view.MediaFrame.Manage=c(12),d.view.Attachment.Details.TwoColumn=c(13),d.view.MediaFrame.Manage.Router=c(14),d.view.EditImage.Details=c(15),d.view.MediaFrame.EditAttachments=c(16),d.view.SelectModeToggleButton=c(17),d.view.DeleteSelectedButton=c(18),d.view.DeleteSelectedPermanentlyButton=c(19)},function(a,b){var c,d=wp.media.view.l10n;c=wp.media.controller.State.extend({defaults:{id:"edit-attachment",title:d.attachmentDetails,content:"edit-metadata",menu:!1,toolbar:!1,router:!1}}),a.exports=c},function(a,b){var c,d=wp.media.view.MediaFrame,e=wp.media.controller.Library,f=Backbone.$;c=d.extend({initialize:function(){_.defaults(this.options,{title:"",modal:!1,selection:[],library:{},multiple:"add",state:"library",uploader:!0,mode:["grid","edit"]}),this.$body=f(document.body),this.$window=f(window),this.$adminBar=f("#wpadminbar"),this.$uploaderToggler=f(".page-title-action").attr("aria-expanded","false").on("click",_.bind(this.addNewClickHandler,this)),this.$window.on("scroll resize",_.debounce(_.bind(this.fixPosition,this),15)),this.$el.addClass("wp-core-ui"),!wp.Uploader.limitExceeded&&wp.Uploader.browser.supported||(this.options.uploader=!1),this.options.uploader&&(this.uploader=new wp.media.view.UploaderWindow({controller:this,uploader:{dropzone:document.body,container:document.body}}).render(),this.uploader.ready(),f("body").append(this.uploader.el),this.options.uploader=!1),this.gridRouter=new wp.media.view.MediaFrame.Manage.Router,d.prototype.initialize.apply(this,arguments),this.$el.appendTo(this.options.container),this.createStates(),this.bindRegionModeHandlers(),this.render(),this.bindSearchHandler(),wp.media.frames.browse=this},bindSearchHandler:function(){var a=this.$("#media-search-input"),b=this.browserView.toolbar.get("search").$el,c=this.$(".view-list"),d=_.throttle(function(a){var b=f(a.currentTarget).val(),c="";b&&(c+="?search="+b,this.gridRouter.navigate(this.gridRouter.baseUrl(c),{replace:!0}))},1e3);a.on("input",_.bind(d,this)),this.gridRouter.on("route:search",function(){var a=window.location.href;a.indexOf("mode=")>-1?a=a.replace(/mode=[^&]+/g,"mode=list"):a+=a.indexOf("?")>-1?"&mode=list":"?mode=list",a=a.replace("search=","s="),c.prop("href",a)}).on("route:reset",function(){b.val("").trigger("input")})},createStates:function(){var a=this.options;this.options.states||this.states.add([new e({library:wp.media.query(a.library),multiple:a.multiple,title:a.title,content:"browse",toolbar:"select",contentUserSetting:!1,filterable:"all",autoSelect:!1})])},bindRegionModeHandlers:function(){this.on("content:create:browse",this.browseContent,this),this.on("edit:attachment",this.openEditAttachmentModal,this),this.on("select:activate",this.bindKeydown,this),this.on("select:deactivate",this.unbindKeydown,this)},handleKeydown:function(a){27===a.which&&(a.preventDefault(),this.deactivateMode("select").activateMode("edit"))},bindKeydown:function(){this.$body.on("keydown.select",_.bind(this.handleKeydown,this))},unbindKeydown:function(){this.$body.off("keydown.select")},fixPosition:function(){var a,b;this.isModeActive("select")&&(a=this.$(".attachments-browser"),b=a.find(".media-toolbar"),a.offset().top+16<this.$window.scrollTop()+this.$adminBar.height()?(a.addClass("fixed"),b.css("width",a.width()+"px")):(a.removeClass("fixed"),b.css("width","")))},addNewClickHandler:function(a){a.preventDefault(),this.trigger("toggle:upload:attachment"),this.uploader&&this.uploader.refresh()},openEditAttachmentModal:function(a){wp.media.frames.edit?wp.media.frames.edit.open().trigger("refresh",a):wp.media.frames.edit=wp.media({frame:"edit-attachments",controller:this,library:this.state().get("library"),model:a})},browseContent:function(a){var b=this.state();this.browserView=a.view=new wp.media.view.AttachmentsBrowser({controller:this,collection:b.get("library"),selection:b.get("selection"),model:b,sortable:b.get("sortable"),search:b.get("searchable"),filters:b.get("filterable"),date:b.get("date"),display:b.get("displaySettings"),dragInfo:b.get("dragInfo"),sidebar:"errors",suggestedWidth:b.get("suggestedWidth"),suggestedHeight:b.get("suggestedHeight"),AttachmentView:b.get("AttachmentView"),scrollElement:document}),this.browserView.on("ready",_.bind(this.bindDeferred,this)),this.errors=wp.Uploader.errors,this.errors.on("add remove reset",this.sidebarVisibility,this)},sidebarVisibility:function(){this.browserView.$(".media-sidebar").toggle(!!this.errors.length)},bindDeferred:function(){this.browserView.dfd&&this.browserView.dfd.done(_.bind(this.startHistory,this))},startHistory:function(){window.history&&window.history.pushState&&(Backbone.History.started&&Backbone.history.stop(),Backbone.history.start({root:window._wpMediaGridSettings.adminUrl,pushState:!0}))}}),a.exports=c},function(a,b){var c,d=wp.media.view.Attachment.Details;c=d.extend({template:wp.template("attachment-details-two-column"),initialize:function(){this.controller.on("content:activate:edit-details",_.bind(this.editAttachment,this)),d.prototype.initialize.apply(this,arguments)},editAttachment:function(a){a&&a.preventDefault(),this.controller.content.mode("edit-image")},toggleSelectionHandler:function(){},render:function(){d.prototype.render.apply(this,arguments),wp.media.mixin.removeAllPlayers(),this.$("audio, video").each(function(a,b){var c=wp.media.view.MediaDetails.prepareSrc(b);new window.MediaElementPlayer(c,wp.media.mixin.mejsSettings)})}}),a.exports=c},function(a,b){var c=Backbone.Router.extend({routes:{"media?item=:slug&mode=edit":"editItem","media?item=:slug":"showItem","media?search=:query":"search","media":"reset"},baseUrl:function(a){return"media"+a},reset:function(){var a=wp.media.frames.edit;a&&a.close()},search:function(a){jQuery("#media-search-input").val(a).trigger("input")},showItem:function(a){var b,c=wp.media,d=c.frames.browse,e=d.state().get("library");b=e.findWhere({id:parseInt(a,10)}),b.set("skipHistory",!0),b?d.trigger("edit:attachment",b):(b=c.attachment(a),d.listenTo(b,"change",function(a){d.stopListening(b),d.trigger("edit:attachment",a)}),b.fetch())},editItem:function(a){this.showItem(a),wp.media.frames.edit.content.mode("edit-details")}});a.exports=c},function(a,b){var c,d=wp.media.View,e=wp.media.view.EditImage;c=e.extend({initialize:function(a){this.editor=window.imageEdit,this.frame=a.frame,this.controller=a.controller,d.prototype.initialize.apply(this,arguments)},back:function(){this.frame.content.mode("edit-metadata")},save:function(){this.model.fetch().done(_.bind(function(){this.frame.content.mode("edit-metadata")},this))}}),a.exports=c},function(a,b){var c,d=wp.media.view.Frame,e=wp.media.view.MediaFrame,f=jQuery;c=e.extend({className:"edit-attachment-frame",template:wp.template("edit-attachment-frame"),regions:["title","content"],events:{"click .left":"previousMediaItem","click .right":"nextMediaItem"},initialize:function(){d.prototype.initialize.apply(this,arguments),_.defaults(this.options,{modal:!0,state:"edit-attachment"}),this.controller=this.options.controller,this.gridRouter=this.controller.gridRouter,this.library=this.options.library,this.options.model&&(this.model=this.options.model),this.bindHandlers(),this.createStates(),this.createModal(),this.title.mode("default"),this.toggleNav()},bindHandlers:function(){this.on("title:create:default",this.createTitle,this),this.on("content:create:edit-metadata",this.editMetadataMode,this),this.on("content:create:edit-image",this.editImageMode,this),this.on("content:render:edit-image",this.editImageModeRender,this),this.on("refresh",this.rerender,this),this.on("close",this.detach),this.bindModelHandlers(),this.listenTo(this.gridRouter,"route:search",this.close,this)},bindModelHandlers:function(){this.listenTo(this.model,"change:status destroy",this.close,this)},createModal:function(){this.options.modal&&(this.modal=new wp.media.view.Modal({controller:this,title:this.options.title}),this.modal.on("open",_.bind(function(){f("body").on("keydown.media-modal",_.bind(this.keyEvent,this))},this)),this.modal.on("close",_.bind(function(){f("body").off("keydown.media-modal"),f('li.attachment[data-id="'+this.model.get("id")+'"]').focus(),this.resetRoute()},this)),this.modal.content(this),this.modal.open())},createStates:function(){this.states.add([new wp.media.controller.EditAttachmentMetadata({model:this.model,library:this.library})])},editMetadataMode:function(a){a.view=new wp.media.view.Attachment.Details.TwoColumn({controller:this,model:this.model}),a.view.views.set(".attachment-compat",new wp.media.view.AttachmentCompat({controller:this,model:this.model})),this.model&&!this.model.get("skipHistory")&&this.gridRouter.navigate(this.gridRouter.baseUrl("?item="+this.model.id))},editImageMode:function(a){var b=new wp.media.controller.EditImage({model:this.model,frame:this});b._toolbar=function(){},b._router=function(){},b._menu=function(){},a.view=new wp.media.view.EditImage.Details({model:this.model,frame:this,controller:b}),this.gridRouter.navigate(this.gridRouter.baseUrl("?item="+this.model.id+"&mode=edit"))},editImageModeRender:function(a){a.on("ready",a.loadEditor)},toggleNav:function(){this.$(".left").toggleClass("disabled",!this.hasPrevious()),this.$(".right").toggleClass("disabled",!this.hasNext())},rerender:function(a){this.stopListening(this.model),this.model=a,this.bindModelHandlers(),"edit-metadata"!==this.content.mode()?this.content.mode("edit-metadata"):this.content.render(),this.toggleNav()},previousMediaItem:function(){this.hasPrevious()&&(this.trigger("refresh",this.library.at(this.getCurrentIndex()-1)),this.$(".left").focus())},nextMediaItem:function(){this.hasNext()&&(this.trigger("refresh",this.library.at(this.getCurrentIndex()+1)),this.$(".right").focus())},getCurrentIndex:function(){return this.library.indexOf(this.model)},hasNext:function(){return this.getCurrentIndex()+1<this.library.length},hasPrevious:function(){return this.getCurrentIndex()-1>-1},keyEvent:function(a){("INPUT"!==a.target.nodeName&&"TEXTAREA"!==a.target.nodeName||a.target.readOnly||a.target.disabled)&&(39===a.keyCode&&this.nextMediaItem(),37===a.keyCode&&this.previousMediaItem())},resetRoute:function(){var a=this.controller.browserView.toolbar.get("search").$el.val(),b=""!==a?"?search="+a:"";this.gridRouter.navigate(this.gridRouter.baseUrl(b),{replace:!0})}}),a.exports=c},function(a,b){var c,d=wp.media.view.Button,e=wp.media.view.l10n;c=d.extend({initialize:function(){_.defaults(this.options,{size:""}),d.prototype.initialize.apply(this,arguments),this.controller.on("select:activate select:deactivate",this.toggleBulkEditHandler,this),this.controller.on("selection:action:done",this.back,this)},back:function(){this.controller.deactivateMode("select").activateMode("edit")},click:function(){d.prototype.click.apply(this,arguments),this.controller.isModeActive("select")?this.back():this.controller.deactivateMode("edit").activateMode("select")},render:function(){return d.prototype.render.apply(this,arguments),this.$el.addClass("select-mode-toggle-button"),this},toggleBulkEditHandler:function(){var a,b=this.controller.content.get().toolbar;a=b.$(".media-toolbar-secondary > *, .media-toolbar-primary > *"),this.controller.isModeActive("select")?(this.model.set({size:"large",text:e.cancelSelection}),a.not(".spinner, .media-button").hide(),this.$el.show(),b.$(".delete-selected-button").removeClass("hidden")):(this.model.set({size:"",text:e.bulkSelect}),this.controller.content.get().$el.removeClass("fixed"),b.$el.css("width",""),b.$(".delete-selected-button").addClass("hidden"),a.not(".media-button").show(),this.controller.state().get("selection").reset())}}),a.exports=c},function(a,b){var c,d=wp.media.view.Button,e=wp.media.view.l10n;c=d.extend({initialize:function(){d.prototype.initialize.apply(this,arguments),this.options.filters&&this.options.filters.model.on("change",this.filterChange,this),this.controller.on("selection:toggle",this.toggleDisabled,this)},filterChange:function(a){"trash"===a.get("status")?this.model.set("text",e.untrashSelected):wp.media.view.settings.mediaTrash?this.model.set("text",e.trashSelected):this.model.set("text",e.deleteSelected)},toggleDisabled:function(){this.model.set("disabled",!this.controller.state().get("selection").length)},render:function(){return d.prototype.render.apply(this,arguments),this.controller.isModeActive("select")?this.$el.addClass("delete-selected-button"):this.$el.addClass("delete-selected-button hidden"),this.toggleDisabled(),this}}),a.exports=c},function(a,b){var c,d=wp.media.view.Button,e=wp.media.view.DeleteSelectedButton;c=e.extend({initialize:function(){e.prototype.initialize.apply(this,arguments),this.controller.on("select:activate",this.selectActivate,this),this.controller.on("select:deactivate",this.selectDeactivate,this)},filterChange:function(a){this.canShow="trash"===a.get("status")},selectActivate:function(){this.toggleDisabled(),this.$el.toggleClass("hidden",!this.canShow)},selectDeactivate:function(){this.toggleDisabled(),this.$el.addClass("hidden")},render:function(){return d.prototype.render.apply(this,arguments),this.selectActivate(),this}}),a.exports=c}]);
var findPosts;!function(a){findPosts={open:function(b,c){var d=a(".ui-find-overlay");return 0===d.length&&(a("body").append('<div class="ui-find-overlay"></div>'),findPosts.overlay()),d.show(),b&&c&&a("#affected").attr("name",b).val(c),a("#find-posts").show(),a("#find-posts-input").focus().keyup(function(a){27==a.which&&findPosts.close()}),findPosts.send(),!1},close:function(){a("#find-posts-response").empty(),a("#find-posts").hide(),a(".ui-find-overlay").hide()},overlay:function(){a(".ui-find-overlay").on("click",function(){findPosts.close()})},send:function(){var b={ps:a("#find-posts-input").val(),action:"find_posts",_ajax_nonce:a("#_ajax_nonce").val()},c=a(".find-box-search .spinner");c.addClass("is-active"),a.ajax(ajaxurl,{type:"POST",data:b,dataType:"json"}).always(function(){c.removeClass("is-active")}).done(function(b){b.success||a("#find-posts-response").text(attachMediaBoxL10n.error),a("#find-posts-response").html(b.data)}).fail(function(){a("#find-posts-response").text(attachMediaBoxL10n.error)})}},a(document).ready(function(){var b,c=a("#wp-media-grid");c.length&&window.wp&&window.wp.media&&(b=_wpMediaGridSettings,window.wp.media({frame:"manage",container:c,library:b.queryVars}).open()),a("#find-posts-submit").click(function(b){a('#find-posts-response input[type="radio"]:checked').length||b.preventDefault()}),a("#find-posts .find-box-search :input").keypress(function(a){if(13==a.which)return findPosts.send(),!1}),a("#find-posts-search").click(findPosts.send),a("#find-posts-close").click(findPosts.close),a("#doaction, #doaction2").click(function(b){a('select[name^="action"]').each(function(){var c=a(this).val();"attach"===c?(b.preventDefault(),findPosts.open()):"delete"===c&&(showNotice.warn()||b.preventDefault())})}),a(".find-box-inside").on("click","tr",function(){a(this).find(".found-radio input").prop("checked",!0)})})}(jQuery);
/**
 * Attempt to re-color SVG icons used in the admin menu or the toolbar
 *
 */

window.wp = window.wp || {};

wp.svgPainter = ( function( $, window, document, undefined ) {
    'use strict';
    var selector, base64, painter,
        colorscheme = {},
        elements = [];

    $(document).ready( function() {
        // detection for browser SVG capability
        if ( document.implementation.hasFeature( 'http://www.w3.org/TR/SVG11/feature#Image', '1.1' ) ) {
            $( document.body ).removeClass( 'no-svg' ).addClass( 'svg' );
            wp.svgPainter.init();
        }
    });

    /**
     * Needed only for IE9
     *
     * Based on jquery.base64.js 0.0.3 - https://github.com/yckart/jquery.base64.js
     *
     * Based on: https://gist.github.com/Yaffle/1284012
     *
     * Copyright (c) 2012 Yannick Albert (http://yckart.com)
     * Licensed under the MIT license
     * http://www.opensource.org/licenses/mit-license.php
     */
    base64 = ( function() {
        var c,
            b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
            a256 = '',
            r64 = [256],
            r256 = [256],
            i = 0;

        function init() {
            while( i < 256 ) {
                c = String.fromCharCode(i);
                a256 += c;
                r256[i] = i;
                r64[i] = b64.indexOf(c);
                ++i;
            }
        }

        function code( s, discard, alpha, beta, w1, w2 ) {
            var tmp, length,
                buffer = 0,
                i = 0,
                result = '',
                bitsInBuffer = 0;

            s = String(s);
            length = s.length;

            while( i < length ) {
                c = s.charCodeAt(i);
                c = c < 256 ? alpha[c] : -1;

                buffer = ( buffer << w1 ) + c;
                bitsInBuffer += w1;

                while( bitsInBuffer >= w2 ) {
                    bitsInBuffer -= w2;
                    tmp = buffer >> bitsInBuffer;
                    result += beta.charAt(tmp);
                    buffer ^= tmp << bitsInBuffer;
                }
                ++i;
            }

            if ( ! discard && bitsInBuffer > 0 ) {
                result += beta.charAt( buffer << ( w2 - bitsInBuffer ) );
            }

            return result;
        }

        function btoa( plain ) {
            if ( ! c ) {
                init();
            }

            plain = code( plain, false, r256, b64, 8, 6 );
            return plain + '===='.slice( ( plain.length % 4 ) || 4 );
        }

        function atob( coded ) {
            var i;

            if ( ! c ) {
                init();
            }

            coded = coded.replace( /[^A-Za-z0-9\+\/\=]/g, '' );
            coded = String(coded).split('=');
            i = coded.length;

            do {
                --i;
                coded[i] = code( coded[i], true, r64, a256, 6, 8 );
            } while ( i > 0 );

            coded = coded.join('');
            return coded;
        }

        return {
            atob: atob,
            btoa: btoa
        };
    })();

    return {
        init: function() {
            painter = this;
            selector = $( '#adminmenu .wp-menu-image, #wpadminbar .ab-item' );

            this.setColors();
            this.findElements();
            this.paint();
        },

        setColors: function( colors ) {
            if ( typeof colors === 'undefined' && typeof window._wpColorScheme !== 'undefined' ) {
                colors = window._wpColorScheme;
            }

            if ( colors && colors.icons && colors.icons.base && colors.icons.current && colors.icons.focus ) {
                colorscheme = colors.icons;
            }
        },

        findElements: function() {
            selector.each( function() {
                var $this = $(this), bgImage = $this.css( 'background-image' );

                if ( bgImage && bgImage.indexOf( 'data:image/svg+xml;base64' ) != -1 ) {
                    elements.push( $this );
                }
            });
        },

        paint: function() {
            // loop through all elements
            $.each( elements, function( index, $element ) {
                var $menuitem = $element.parent().parent();

                if ( $menuitem.hasClass( 'current' ) || $menuitem.hasClass( 'wp-has-current-submenu' ) ) {
                    // paint icon in 'current' color
                    painter.paintElement( $element, 'current' );
                } else {
                    // paint icon in base color
                    painter.paintElement( $element, 'base' );

                    // set hover callbacks
                    $menuitem.hover(
                        function() {
                            painter.paintElement( $element, 'focus' );
                        },
                        function() {
                            // Match the delay from hoverIntent
                            window.setTimeout( function() {
                                painter.paintElement( $element, 'base' );
                            }, 100 );
                        }
                    );
                }
            });
        },

        paintElement: function( $element, colorType ) {
            var xml, encoded, color;

            if ( ! colorType || ! colorscheme.hasOwnProperty( colorType ) ) {
                return;
            }

            color = colorscheme[ colorType ];

            // only accept hex colors: #101 or #101010
            if ( ! color.match( /^(#[0-9a-f]{3}|#[0-9a-f]{6})$/i ) ) {
                return;
            }

            xml = $element.data( 'wp-ui-svg-' + color );

            if ( xml === 'none' ) {
                return;
            }

            if ( ! xml ) {
                encoded = $element.css( 'background-image' ).match( /.+data:image\/svg\+xml;base64,([A-Za-z0-9\+\/\=]+)/ );

                if ( ! encoded || ! encoded[1] ) {
                    $element.data( 'wp-ui-svg-' + color, 'none' );
                    return;
                }

                try {
                    if ( 'atob' in window ) {
                        xml = window.atob( encoded[1] );
                    } else {
                        xml = base64.atob( encoded[1] );
                    }
                } catch ( error ) {}

                if ( xml ) {
                    // replace `fill` attributes
                    xml = xml.replace( /fill="(.+?)"/g, 'fill="' + color + '"');

                    // replace `style` attributes
                    xml = xml.replace( /style="(.+?)"/g, 'style="fill:' + color + '"');

                    // replace `fill` properties in `<style>` tags
                    xml = xml.replace( /fill:.*?;/g, 'fill: ' + color + ';');

                    if ( 'btoa' in window ) {
                        xml = window.btoa( xml );
                    } else {
                        xml = base64.btoa( xml );
                    }

                    $element.data( 'wp-ui-svg-' + color, xml );
                } else {
                    $element.data( 'wp-ui-svg-' + color, 'none' );
                    return;
                }
            }

            $element.attr( 'style', 'background-image: url("data:image/svg+xml;base64,' + xml + '") !important;' );
        }
    };

})( jQuery, window, document );

!function(a,b,c){var d=function(){function d(){var c,d,f,h;"string"==typeof b.pagenow&&(z.screenId=b.pagenow),"string"==typeof b.ajaxurl&&(z.url=b.ajaxurl),"object"==typeof b.heartbeatSettings&&(c=b.heartbeatSettings,!z.url&&c.ajaxurl&&(z.url=c.ajaxurl),c.interval&&(z.mainInterval=c.interval,z.mainInterval<15?z.mainInterval=15:z.mainInterval>120&&(z.mainInterval=120)),c.minimalInterval&&(c.minimalInterval=parseInt(c.minimalInterval,10),z.minimalInterval=c.minimalInterval>0&&c.minimalInterval<=600?1e3*c.minimalInterval:0),z.minimalInterval&&z.mainInterval<z.minimalInterval&&(z.mainInterval=z.minimalInterval),z.screenId||(z.screenId=c.screenId||"front"),"disable"===c.suspension&&(z.suspendEnabled=!1)),z.mainInterval=1e3*z.mainInterval,z.originalInterval=z.mainInterval,"undefined"!=typeof document.hidden?(d="hidden",h="visibilitychange",f="visibilityState"):"undefined"!=typeof document.msHidden?(d="msHidden",h="msvisibilitychange",f="msVisibilityState"):"undefined"!=typeof document.webkitHidden&&(d="webkitHidden",h="webkitvisibilitychange",f="webkitVisibilityState"),d&&(document[d]&&(z.hasFocus=!1),y.on(h+".wp-heartbeat",function(){"hidden"===document[f]?(l(),b.clearInterval(z.checkFocusTimer)):(m(),document.hasFocus&&(z.checkFocusTimer=b.setInterval(g,1e4)))})),document.hasFocus&&(z.checkFocusTimer=b.setInterval(g,1e4)),a(b).on("unload.wp-heartbeat",function(){z.suspend=!0,z.xhr&&4!==z.xhr.readyState&&z.xhr.abort()}),b.setInterval(o,3e4),y.ready(function(){z.lastTick=e(),k()})}function e(){return(new Date).getTime()}function f(a){var c,d=a.src;if(d&&/^https?:\/\//.test(d)&&(c=b.location.origin?b.location.origin:b.location.protocol+"//"+b.location.host,0!==d.indexOf(c)))return!1;try{if(a.contentWindow.document)return!0}catch(e){}return!1}function g(){z.hasFocus&&!document.hasFocus()?l():!z.hasFocus&&document.hasFocus()&&m()}function h(a,b){var c;if(a){switch(a){case"abort":break;case"timeout":c=!0;break;case"error":if(503===b&&z.hasConnected){c=!0;break}case"parsererror":case"empty":case"unknown":z.errorcount++,z.errorcount>2&&z.hasConnected&&(c=!0)}c&&!q()&&(z.connectionError=!0,y.trigger("heartbeat-connection-lost",[a,b]))}}function i(){z.hasConnected=!0,q()&&(z.errorcount=0,z.connectionError=!1,y.trigger("heartbeat-connection-restored"))}function j(){var c,d;z.connecting||z.suspend||(z.lastTick=e(),d=a.extend({},z.queue),z.queue={},y.trigger("heartbeat-send",[d]),c={data:d,interval:z.tempInterval?z.tempInterval/1e3:z.mainInterval/1e3,_nonce:"object"==typeof b.heartbeatSettings?b.heartbeatSettings.nonce:"",action:"heartbeat",screen_id:z.screenId,has_focus:z.hasFocus},"customize"===z.screenId&&(c.wp_customize="on"),z.connecting=!0,z.xhr=a.ajax({url:z.url,type:"post",timeout:3e4,data:c,dataType:"json"}).always(function(){z.connecting=!1,k()}).done(function(a,b,c){var d;return a?(i(),a.nonces_expired&&y.trigger("heartbeat-nonces-expired"),a.heartbeat_interval&&(d=a.heartbeat_interval,delete a.heartbeat_interval),y.trigger("heartbeat-tick",[a,b,c]),void(d&&t(d))):void h("empty")}).fail(function(a,b,c){h(b||"unknown",a.status),y.trigger("heartbeat-error",[a,b,c])}))}function k(){var a=e()-z.lastTick,c=z.mainInterval;z.suspend||(z.hasFocus?z.countdown>0&&z.tempInterval&&(c=z.tempInterval,z.countdown--,z.countdown<1&&(z.tempInterval=0)):c=12e4,z.minimalInterval&&c<z.minimalInterval&&(c=z.minimalInterval),b.clearTimeout(z.beatTimer),a<c?z.beatTimer=b.setTimeout(function(){j()},c-a):j())}function l(){z.hasFocus=!1}function m(){z.userActivity=e(),z.suspend=!1,z.hasFocus||(z.hasFocus=!0,k())}function n(){z.userActivityEvents=!1,y.off(".wp-heartbeat-active"),a("iframe").each(function(b,c){f(c)&&a(c.contentWindow).off(".wp-heartbeat-active")}),m()}function o(){var b=z.userActivity?e()-z.userActivity:0;b>3e5&&z.hasFocus&&l(),(z.suspendEnabled&&b>6e5||b>36e5)&&(z.suspend=!0),z.userActivityEvents||(y.on("mouseover.wp-heartbeat-active keyup.wp-heartbeat-active touchend.wp-heartbeat-active",function(){n()}),a("iframe").each(function(b,c){f(c)&&a(c.contentWindow).on("mouseover.wp-heartbeat-active keyup.wp-heartbeat-active touchend.wp-heartbeat-active",function(){n()})}),z.userActivityEvents=!0)}function p(){return z.hasFocus}function q(){return z.connectionError}function r(){z.lastTick=0,k()}function s(){z.suspendEnabled=!1}function t(a,b){var c,d=z.tempInterval?z.tempInterval:z.mainInterval;if(a){switch(a){case"fast":case 5:c=5e3;break;case 15:c=15e3;break;case 30:c=3e4;break;case 60:c=6e4;break;case 120:c=12e4;break;case"long-polling":return z.mainInterval=0,0;default:c=z.originalInterval}z.minimalInterval&&c<z.minimalInterval&&(c=z.minimalInterval),5e3===c?(b=parseInt(b,10)||30,b=b<1||b>30?30:b,z.countdown=b,z.tempInterval=c):(z.countdown=0,z.tempInterval=0,z.mainInterval=c),c!==d&&k()}return z.tempInterval?z.tempInterval/1e3:z.mainInterval/1e3}function u(a,b,c){return!!a&&((!c||!this.isQueued(a))&&(z.queue[a]=b,!0))}function v(a){if(a)return z.queue.hasOwnProperty(a)}function w(a){a&&delete z.queue[a]}function x(a){if(a)return this.isQueued(a)?z.queue[a]:c}var y=a(document),z={suspend:!1,suspendEnabled:!0,screenId:"",url:"",lastTick:0,queue:{},mainInterval:60,tempInterval:0,originalInterval:0,minimalInterval:0,countdown:0,connecting:!1,connectionError:!1,errorcount:0,hasConnected:!1,hasFocus:!0,userActivity:0,userActivityEvents:!1,checkFocusTimer:0,beatTimer:0};return d(),{hasFocus:p,connectNow:r,disableSuspend:s,interval:t,hasConnectionError:q,enqueue:u,dequeue:w,isQueued:v,getQueuedItem:x}};b.wp=b.wp||{},b.wp.heartbeat=new d}(jQuery,window);
!function(a){function b(){var b,d=a("#wp-auth-check"),f=a("#wp-auth-check-form"),g=e.find(".wp-auth-fallback-expired"),h=!1;f.length&&(a(window).on("beforeunload.wp-auth-check",function(a){a.originalEvent.returnValue=window.authcheckL10n.beforeunload}),b=a('<iframe id="wp-auth-check-frame" frameborder="0">').attr("title",g.text()),b.on("load",function(){var b,i;h=!0,f.removeClass("loading");try{i=a(this).contents().find("body"),b=i.height()}catch(j){return e.addClass("fallback"),d.css("max-height",""),f.remove(),void g.focus()}b?i&&i.hasClass("interim-login-success")?c():d.css("max-height",b+40+"px"):i&&i.length||(e.addClass("fallback"),d.css("max-height",""),f.remove(),g.focus())}).attr("src",f.data("src")),f.append(b)),a("body").addClass("modal-open"),e.removeClass("hidden"),b?(b.focus(),setTimeout(function(){h||(e.addClass("fallback"),f.remove(),g.focus())},1e4)):g.focus()}function c(){a(window).off("beforeunload.wp-auth-check"),"undefined"==typeof adminpage||"post-php"!==adminpage&&"post-new-php"!==adminpage||"undefined"==typeof wp||!wp.heartbeat||(a(document).off("heartbeat-tick.wp-auth-check"),wp.heartbeat.connectNow()),e.fadeOut(200,function(){e.addClass("hidden").css("display",""),a("#wp-auth-check-frame").remove(),a("body").removeClass("modal-open")})}function d(){var a=parseInt(window.authcheckL10n.interval,10)||180;f=(new Date).getTime()+1e3*a}var e,f;a(document).on("heartbeat-tick.wp-auth-check",function(a,f){"wp-auth-check"in f&&(d(),!f["wp-auth-check"]&&e.hasClass("hidden")?b():f["wp-auth-check"]&&!e.hasClass("hidden")&&c())}).on("heartbeat-send.wp-auth-check",function(a,b){(new Date).getTime()>f&&(b["wp-auth-check"]=!0)}).ready(function(){d(),e=a("#wp-auth-check-wrap"),e.find(".wp-auth-check-close").on("click",function(){c()})})}(jQuery);
/*!
 * jQuery UI Draggable 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/draggable/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./core","./mouse","./widget"],a):a(jQuery)}(function(a){return a.widget("ui.draggable",a.ui.mouse,{version:"1.11.4",widgetEventPrefix:"drag",options:{addClasses:!0,appendTo:"parent",axis:!1,connectToSortable:!1,containment:!1,cursor:"auto",cursorAt:!1,grid:!1,handle:!1,helper:"original",iframeFix:!1,opacity:!1,refreshPositions:!1,revert:!1,revertDuration:500,scope:"default",scroll:!0,scrollSensitivity:20,scrollSpeed:20,snap:!1,snapMode:"both",snapTolerance:20,stack:!1,zIndex:!1,drag:null,start:null,stop:null},_create:function(){"original"===this.options.helper&&this._setPositionRelative(),this.options.addClasses&&this.element.addClass("ui-draggable"),this.options.disabled&&this.element.addClass("ui-draggable-disabled"),this._setHandleClassName(),this._mouseInit()},_setOption:function(a,b){this._super(a,b),"handle"===a&&(this._removeHandleClassName(),this._setHandleClassName())},_destroy:function(){return(this.helper||this.element).is(".ui-draggable-dragging")?void(this.destroyOnClear=!0):(this.element.removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"),this._removeHandleClassName(),void this._mouseDestroy())},_mouseCapture:function(b){var c=this.options;return this._blurActiveElement(b),!(this.helper||c.disabled||a(b.target).closest(".ui-resizable-handle").length>0)&&(this.handle=this._getHandle(b),!!this.handle&&(this._blockFrames(c.iframeFix===!0?"iframe":c.iframeFix),!0))},_blockFrames:function(b){this.iframeBlocks=this.document.find(b).map(function(){var b=a(this);return a("<div>").css("position","absolute").appendTo(b.parent()).outerWidth(b.outerWidth()).outerHeight(b.outerHeight()).offset(b.offset())[0]})},_unblockFrames:function(){this.iframeBlocks&&(this.iframeBlocks.remove(),delete this.iframeBlocks)},_blurActiveElement:function(b){var c=this.document[0];if(this.handleElement.is(b.target))try{c.activeElement&&"body"!==c.activeElement.nodeName.toLowerCase()&&a(c.activeElement).blur()}catch(d){}},_mouseStart:function(b){var c=this.options;return this.helper=this._createHelper(b),this.helper.addClass("ui-draggable-dragging"),this._cacheHelperProportions(),a.ui.ddmanager&&(a.ui.ddmanager.current=this),this._cacheMargins(),this.cssPosition=this.helper.css("position"),this.scrollParent=this.helper.scrollParent(!0),this.offsetParent=this.helper.offsetParent(),this.hasFixedAncestor=this.helper.parents().filter(function(){return"fixed"===a(this).css("position")}).length>0,this.positionAbs=this.element.offset(),this._refreshOffsets(b),this.originalPosition=this.position=this._generatePosition(b,!1),this.originalPageX=b.pageX,this.originalPageY=b.pageY,c.cursorAt&&this._adjustOffsetFromHelper(c.cursorAt),this._setContainment(),this._trigger("start",b)===!1?(this._clear(),!1):(this._cacheHelperProportions(),a.ui.ddmanager&&!c.dropBehaviour&&a.ui.ddmanager.prepareOffsets(this,b),this._normalizeRightBottom(),this._mouseDrag(b,!0),a.ui.ddmanager&&a.ui.ddmanager.dragStart(this,b),!0)},_refreshOffsets:function(a){this.offset={top:this.positionAbs.top-this.margins.top,left:this.positionAbs.left-this.margins.left,scroll:!1,parent:this._getParentOffset(),relative:this._getRelativeOffset()},this.offset.click={left:a.pageX-this.offset.left,top:a.pageY-this.offset.top}},_mouseDrag:function(b,c){if(this.hasFixedAncestor&&(this.offset.parent=this._getParentOffset()),this.position=this._generatePosition(b,!0),this.positionAbs=this._convertPositionTo("absolute"),!c){var d=this._uiHash();if(this._trigger("drag",b,d)===!1)return this._mouseUp({}),!1;this.position=d.position}return this.helper[0].style.left=this.position.left+"px",this.helper[0].style.top=this.position.top+"px",a.ui.ddmanager&&a.ui.ddmanager.drag(this,b),!1},_mouseStop:function(b){var c=this,d=!1;return a.ui.ddmanager&&!this.options.dropBehaviour&&(d=a.ui.ddmanager.drop(this,b)),this.dropped&&(d=this.dropped,this.dropped=!1),"invalid"===this.options.revert&&!d||"valid"===this.options.revert&&d||this.options.revert===!0||a.isFunction(this.options.revert)&&this.options.revert.call(this.element,d)?a(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){c._trigger("stop",b)!==!1&&c._clear()}):this._trigger("stop",b)!==!1&&this._clear(),!1},_mouseUp:function(b){return this._unblockFrames(),a.ui.ddmanager&&a.ui.ddmanager.dragStop(this,b),this.handleElement.is(b.target)&&this.element.focus(),a.ui.mouse.prototype._mouseUp.call(this,b)},cancel:function(){return this.helper.is(".ui-draggable-dragging")?this._mouseUp({}):this._clear(),this},_getHandle:function(b){return!this.options.handle||!!a(b.target).closest(this.element.find(this.options.handle)).length},_setHandleClassName:function(){this.handleElement=this.options.handle?this.element.find(this.options.handle):this.element,this.handleElement.addClass("ui-draggable-handle")},_removeHandleClassName:function(){this.handleElement.removeClass("ui-draggable-handle")},_createHelper:function(b){var c=this.options,d=a.isFunction(c.helper),e=d?a(c.helper.apply(this.element[0],[b])):"clone"===c.helper?this.element.clone().removeAttr("id"):this.element;return e.parents("body").length||e.appendTo("parent"===c.appendTo?this.element[0].parentNode:c.appendTo),d&&e[0]===this.element[0]&&this._setPositionRelative(),e[0]===this.element[0]||/(fixed|absolute)/.test(e.css("position"))||e.css("position","absolute"),e},_setPositionRelative:function(){/^(?:r|a|f)/.test(this.element.css("position"))||(this.element[0].style.position="relative")},_adjustOffsetFromHelper:function(b){"string"==typeof b&&(b=b.split(" ")),a.isArray(b)&&(b={left:+b[0],top:+b[1]||0}),"left"in b&&(this.offset.click.left=b.left+this.margins.left),"right"in b&&(this.offset.click.left=this.helperProportions.width-b.right+this.margins.left),"top"in b&&(this.offset.click.top=b.top+this.margins.top),"bottom"in b&&(this.offset.click.top=this.helperProportions.height-b.bottom+this.margins.top)},_isRootNode:function(a){return/(html|body)/i.test(a.tagName)||a===this.document[0]},_getParentOffset:function(){var b=this.offsetParent.offset(),c=this.document[0];return"absolute"===this.cssPosition&&this.scrollParent[0]!==c&&a.contains(this.scrollParent[0],this.offsetParent[0])&&(b.left+=this.scrollParent.scrollLeft(),b.top+=this.scrollParent.scrollTop()),this._isRootNode(this.offsetParent[0])&&(b={top:0,left:0}),{top:b.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:b.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if("relative"!==this.cssPosition)return{top:0,left:0};var a=this.element.position(),b=this._isRootNode(this.scrollParent[0]);return{top:a.top-(parseInt(this.helper.css("top"),10)||0)+(b?0:this.scrollParent.scrollTop()),left:a.left-(parseInt(this.helper.css("left"),10)||0)+(b?0:this.scrollParent.scrollLeft())}},_cacheMargins:function(){this.margins={left:parseInt(this.element.css("marginLeft"),10)||0,top:parseInt(this.element.css("marginTop"),10)||0,right:parseInt(this.element.css("marginRight"),10)||0,bottom:parseInt(this.element.css("marginBottom"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var b,c,d,e=this.options,f=this.document[0];return this.relativeContainer=null,e.containment?"window"===e.containment?void(this.containment=[a(window).scrollLeft()-this.offset.relative.left-this.offset.parent.left,a(window).scrollTop()-this.offset.relative.top-this.offset.parent.top,a(window).scrollLeft()+a(window).width()-this.helperProportions.width-this.margins.left,a(window).scrollTop()+(a(window).height()||f.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]):"document"===e.containment?void(this.containment=[0,0,a(f).width()-this.helperProportions.width-this.margins.left,(a(f).height()||f.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top]):e.containment.constructor===Array?void(this.containment=e.containment):("parent"===e.containment&&(e.containment=this.helper[0].parentNode),c=a(e.containment),d=c[0],void(d&&(b=/(scroll|auto)/.test(c.css("overflow")),this.containment=[(parseInt(c.css("borderLeftWidth"),10)||0)+(parseInt(c.css("paddingLeft"),10)||0),(parseInt(c.css("borderTopWidth"),10)||0)+(parseInt(c.css("paddingTop"),10)||0),(b?Math.max(d.scrollWidth,d.offsetWidth):d.offsetWidth)-(parseInt(c.css("borderRightWidth"),10)||0)-(parseInt(c.css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left-this.margins.right,(b?Math.max(d.scrollHeight,d.offsetHeight):d.offsetHeight)-(parseInt(c.css("borderBottomWidth"),10)||0)-(parseInt(c.css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top-this.margins.bottom],this.relativeContainer=c))):void(this.containment=null)},_convertPositionTo:function(a,b){b||(b=this.position);var c="absolute"===a?1:-1,d=this._isRootNode(this.scrollParent[0]);return{top:b.top+this.offset.relative.top*c+this.offset.parent.top*c-("fixed"===this.cssPosition?-this.offset.scroll.top:d?0:this.offset.scroll.top)*c,left:b.left+this.offset.relative.left*c+this.offset.parent.left*c-("fixed"===this.cssPosition?-this.offset.scroll.left:d?0:this.offset.scroll.left)*c}},_generatePosition:function(a,b){var c,d,e,f,g=this.options,h=this._isRootNode(this.scrollParent[0]),i=a.pageX,j=a.pageY;return h&&this.offset.scroll||(this.offset.scroll={top:this.scrollParent.scrollTop(),left:this.scrollParent.scrollLeft()}),b&&(this.containment&&(this.relativeContainer?(d=this.relativeContainer.offset(),c=[this.containment[0]+d.left,this.containment[1]+d.top,this.containment[2]+d.left,this.containment[3]+d.top]):c=this.containment,a.pageX-this.offset.click.left<c[0]&&(i=c[0]+this.offset.click.left),a.pageY-this.offset.click.top<c[1]&&(j=c[1]+this.offset.click.top),a.pageX-this.offset.click.left>c[2]&&(i=c[2]+this.offset.click.left),a.pageY-this.offset.click.top>c[3]&&(j=c[3]+this.offset.click.top)),g.grid&&(e=g.grid[1]?this.originalPageY+Math.round((j-this.originalPageY)/g.grid[1])*g.grid[1]:this.originalPageY,j=c?e-this.offset.click.top>=c[1]||e-this.offset.click.top>c[3]?e:e-this.offset.click.top>=c[1]?e-g.grid[1]:e+g.grid[1]:e,f=g.grid[0]?this.originalPageX+Math.round((i-this.originalPageX)/g.grid[0])*g.grid[0]:this.originalPageX,i=c?f-this.offset.click.left>=c[0]||f-this.offset.click.left>c[2]?f:f-this.offset.click.left>=c[0]?f-g.grid[0]:f+g.grid[0]:f),"y"===g.axis&&(i=this.originalPageX),"x"===g.axis&&(j=this.originalPageY)),{top:j-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+("fixed"===this.cssPosition?-this.offset.scroll.top:h?0:this.offset.scroll.top),left:i-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+("fixed"===this.cssPosition?-this.offset.scroll.left:h?0:this.offset.scroll.left)}},_clear:function(){this.helper.removeClass("ui-draggable-dragging"),this.helper[0]===this.element[0]||this.cancelHelperRemoval||this.helper.remove(),this.helper=null,this.cancelHelperRemoval=!1,this.destroyOnClear&&this.destroy()},_normalizeRightBottom:function(){"y"!==this.options.axis&&"auto"!==this.helper.css("right")&&(this.helper.width(this.helper.width()),this.helper.css("right","auto")),"x"!==this.options.axis&&"auto"!==this.helper.css("bottom")&&(this.helper.height(this.helper.height()),this.helper.css("bottom","auto"))},_trigger:function(b,c,d){return d=d||this._uiHash(),a.ui.plugin.call(this,b,[c,d,this],!0),/^(drag|start|stop)/.test(b)&&(this.positionAbs=this._convertPositionTo("absolute"),d.offset=this.positionAbs),a.Widget.prototype._trigger.call(this,b,c,d)},plugins:{},_uiHash:function(){return{helper:this.helper,position:this.position,originalPosition:this.originalPosition,offset:this.positionAbs}}}),a.ui.plugin.add("draggable","connectToSortable",{start:function(b,c,d){var e=a.extend({},c,{item:d.element});d.sortables=[],a(d.options.connectToSortable).each(function(){var c=a(this).sortable("instance");c&&!c.options.disabled&&(d.sortables.push(c),c.refreshPositions(),c._trigger("activate",b,e))})},stop:function(b,c,d){var e=a.extend({},c,{item:d.element});d.cancelHelperRemoval=!1,a.each(d.sortables,function(){var a=this;a.isOver?(a.isOver=0,d.cancelHelperRemoval=!0,a.cancelHelperRemoval=!1,a._storedCSS={position:a.placeholder.css("position"),top:a.placeholder.css("top"),left:a.placeholder.css("left")},a._mouseStop(b),a.options.helper=a.options._helper):(a.cancelHelperRemoval=!0,a._trigger("deactivate",b,e))})},drag:function(b,c,d){a.each(d.sortables,function(){var e=!1,f=this;f.positionAbs=d.positionAbs,f.helperProportions=d.helperProportions,f.offset.click=d.offset.click,f._intersectsWith(f.containerCache)&&(e=!0,a.each(d.sortables,function(){return this.positionAbs=d.positionAbs,this.helperProportions=d.helperProportions,this.offset.click=d.offset.click,this!==f&&this._intersectsWith(this.containerCache)&&a.contains(f.element[0],this.element[0])&&(e=!1),e})),e?(f.isOver||(f.isOver=1,d._parent=c.helper.parent(),f.currentItem=c.helper.appendTo(f.element).data("ui-sortable-item",!0),f.options._helper=f.options.helper,f.options.helper=function(){return c.helper[0]},b.target=f.currentItem[0],f._mouseCapture(b,!0),f._mouseStart(b,!0,!0),f.offset.click.top=d.offset.click.top,f.offset.click.left=d.offset.click.left,f.offset.parent.left-=d.offset.parent.left-f.offset.parent.left,f.offset.parent.top-=d.offset.parent.top-f.offset.parent.top,d._trigger("toSortable",b),d.dropped=f.element,a.each(d.sortables,function(){this.refreshPositions()}),d.currentItem=d.element,f.fromOutside=d),f.currentItem&&(f._mouseDrag(b),c.position=f.position)):f.isOver&&(f.isOver=0,f.cancelHelperRemoval=!0,f.options._revert=f.options.revert,f.options.revert=!1,f._trigger("out",b,f._uiHash(f)),f._mouseStop(b,!0),f.options.revert=f.options._revert,f.options.helper=f.options._helper,f.placeholder&&f.placeholder.remove(),c.helper.appendTo(d._parent),d._refreshOffsets(b),c.position=d._generatePosition(b,!0),d._trigger("fromSortable",b),d.dropped=!1,a.each(d.sortables,function(){this.refreshPositions()}))})}}),a.ui.plugin.add("draggable","cursor",{start:function(b,c,d){var e=a("body"),f=d.options;e.css("cursor")&&(f._cursor=e.css("cursor")),e.css("cursor",f.cursor)},stop:function(b,c,d){var e=d.options;e._cursor&&a("body").css("cursor",e._cursor)}}),a.ui.plugin.add("draggable","opacity",{start:function(b,c,d){var e=a(c.helper),f=d.options;e.css("opacity")&&(f._opacity=e.css("opacity")),e.css("opacity",f.opacity)},stop:function(b,c,d){var e=d.options;e._opacity&&a(c.helper).css("opacity",e._opacity)}}),a.ui.plugin.add("draggable","scroll",{start:function(a,b,c){c.scrollParentNotHidden||(c.scrollParentNotHidden=c.helper.scrollParent(!1)),c.scrollParentNotHidden[0]!==c.document[0]&&"HTML"!==c.scrollParentNotHidden[0].tagName&&(c.overflowOffset=c.scrollParentNotHidden.offset())},drag:function(b,c,d){var e=d.options,f=!1,g=d.scrollParentNotHidden[0],h=d.document[0];g!==h&&"HTML"!==g.tagName?(e.axis&&"x"===e.axis||(d.overflowOffset.top+g.offsetHeight-b.pageY<e.scrollSensitivity?g.scrollTop=f=g.scrollTop+e.scrollSpeed:b.pageY-d.overflowOffset.top<e.scrollSensitivity&&(g.scrollTop=f=g.scrollTop-e.scrollSpeed)),e.axis&&"y"===e.axis||(d.overflowOffset.left+g.offsetWidth-b.pageX<e.scrollSensitivity?g.scrollLeft=f=g.scrollLeft+e.scrollSpeed:b.pageX-d.overflowOffset.left<e.scrollSensitivity&&(g.scrollLeft=f=g.scrollLeft-e.scrollSpeed))):(e.axis&&"x"===e.axis||(b.pageY-a(h).scrollTop()<e.scrollSensitivity?f=a(h).scrollTop(a(h).scrollTop()-e.scrollSpeed):a(window).height()-(b.pageY-a(h).scrollTop())<e.scrollSensitivity&&(f=a(h).scrollTop(a(h).scrollTop()+e.scrollSpeed))),e.axis&&"y"===e.axis||(b.pageX-a(h).scrollLeft()<e.scrollSensitivity?f=a(h).scrollLeft(a(h).scrollLeft()-e.scrollSpeed):a(window).width()-(b.pageX-a(h).scrollLeft())<e.scrollSensitivity&&(f=a(h).scrollLeft(a(h).scrollLeft()+e.scrollSpeed)))),f!==!1&&a.ui.ddmanager&&!e.dropBehaviour&&a.ui.ddmanager.prepareOffsets(d,b)}}),a.ui.plugin.add("draggable","snap",{start:function(b,c,d){var e=d.options;d.snapElements=[],a(e.snap.constructor!==String?e.snap.items||":data(ui-draggable)":e.snap).each(function(){var b=a(this),c=b.offset();this!==d.element[0]&&d.snapElements.push({item:this,width:b.outerWidth(),height:b.outerHeight(),top:c.top,left:c.left})})},drag:function(b,c,d){var e,f,g,h,i,j,k,l,m,n,o=d.options,p=o.snapTolerance,q=c.offset.left,r=q+d.helperProportions.width,s=c.offset.top,t=s+d.helperProportions.height;for(m=d.snapElements.length-1;m>=0;m--)i=d.snapElements[m].left-d.margins.left,j=i+d.snapElements[m].width,k=d.snapElements[m].top-d.margins.top,l=k+d.snapElements[m].height,r<i-p||q>j+p||t<k-p||s>l+p||!a.contains(d.snapElements[m].item.ownerDocument,d.snapElements[m].item)?(d.snapElements[m].snapping&&d.options.snap.release&&d.options.snap.release.call(d.element,b,a.extend(d._uiHash(),{snapItem:d.snapElements[m].item})),d.snapElements[m].snapping=!1):("inner"!==o.snapMode&&(e=Math.abs(k-t)<=p,f=Math.abs(l-s)<=p,g=Math.abs(i-r)<=p,h=Math.abs(j-q)<=p,e&&(c.position.top=d._convertPositionTo("relative",{top:k-d.helperProportions.height,left:0}).top),f&&(c.position.top=d._convertPositionTo("relative",{top:l,left:0}).top),g&&(c.position.left=d._convertPositionTo("relative",{top:0,left:i-d.helperProportions.width}).left),h&&(c.position.left=d._convertPositionTo("relative",{top:0,left:j}).left)),n=e||f||g||h,"outer"!==o.snapMode&&(e=Math.abs(k-s)<=p,f=Math.abs(l-t)<=p,g=Math.abs(i-q)<=p,h=Math.abs(j-r)<=p,e&&(c.position.top=d._convertPositionTo("relative",{top:k,left:0}).top),f&&(c.position.top=d._convertPositionTo("relative",{top:l-d.helperProportions.height,left:0}).top),g&&(c.position.left=d._convertPositionTo("relative",{top:0,left:i}).left),h&&(c.position.left=d._convertPositionTo("relative",{top:0,left:j-d.helperProportions.width}).left)),!d.snapElements[m].snapping&&(e||f||g||h||n)&&d.options.snap.snap&&d.options.snap.snap.call(d.element,b,a.extend(d._uiHash(),{snapItem:d.snapElements[m].item})),d.snapElements[m].snapping=e||f||g||h||n)}}),a.ui.plugin.add("draggable","stack",{start:function(b,c,d){var e,f=d.options,g=a.makeArray(a(f.stack)).sort(function(b,c){return(parseInt(a(b).css("zIndex"),10)||0)-(parseInt(a(c).css("zIndex"),10)||0)});g.length&&(e=parseInt(a(g[0]).css("zIndex"),10)||0,a(g).each(function(b){a(this).css("zIndex",e+b)}),this.css("zIndex",e+g.length))}}),a.ui.plugin.add("draggable","zIndex",{start:function(b,c,d){var e=a(c.helper),f=d.options;e.css("zIndex")&&(f._zIndex=e.css("zIndex")),e.css("zIndex",f.zIndex)},stop:function(b,c,d){var e=d.options;e._zIndex&&a(c.helper).css("zIndex",e._zIndex)}}),a.ui.draggable});
/*!
 * jQuery UI Slider 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/slider/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./core","./mouse","./widget"],a):a(jQuery)}(function(a){return a.widget("ui.slider",a.ui.mouse,{version:"1.11.4",widgetEventPrefix:"slide",options:{animate:!1,distance:0,max:100,min:0,orientation:"horizontal",range:!1,step:1,value:0,values:null,change:null,slide:null,start:null,stop:null},numPages:5,_create:function(){this._keySliding=!1,this._mouseSliding=!1,this._animateOff=!0,this._handleIndex=null,this._detectOrientation(),this._mouseInit(),this._calculateNewMax(),this.element.addClass("ui-slider ui-slider-"+this.orientation+" ui-widget ui-widget-content ui-corner-all"),this._refresh(),this._setOption("disabled",this.options.disabled),this._animateOff=!1},_refresh:function(){this._createRange(),this._createHandles(),this._setupEvents(),this._refreshValue()},_createHandles:function(){var b,c,d=this.options,e=this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),f="<span class='ui-slider-handle ui-state-default ui-corner-all' tabindex='0'></span>",g=[];for(c=d.values&&d.values.length||1,e.length>c&&(e.slice(c).remove(),e=e.slice(0,c)),b=e.length;b<c;b++)g.push(f);this.handles=e.add(a(g.join("")).appendTo(this.element)),this.handle=this.handles.eq(0),this.handles.each(function(b){a(this).data("ui-slider-handle-index",b)})},_createRange:function(){var b=this.options,c="";b.range?(b.range===!0&&(b.values?b.values.length&&2!==b.values.length?b.values=[b.values[0],b.values[0]]:a.isArray(b.values)&&(b.values=b.values.slice(0)):b.values=[this._valueMin(),this._valueMin()]),this.range&&this.range.length?this.range.removeClass("ui-slider-range-min ui-slider-range-max").css({left:"",bottom:""}):(this.range=a("<div></div>").appendTo(this.element),c="ui-slider-range ui-widget-header ui-corner-all"),this.range.addClass(c+("min"===b.range||"max"===b.range?" ui-slider-range-"+b.range:""))):(this.range&&this.range.remove(),this.range=null)},_setupEvents:function(){this._off(this.handles),this._on(this.handles,this._handleEvents),this._hoverable(this.handles),this._focusable(this.handles)},_destroy:function(){this.handles.remove(),this.range&&this.range.remove(),this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-widget ui-widget-content ui-corner-all"),this._mouseDestroy()},_mouseCapture:function(b){var c,d,e,f,g,h,i,j,k=this,l=this.options;return!l.disabled&&(this.elementSize={width:this.element.outerWidth(),height:this.element.outerHeight()},this.elementOffset=this.element.offset(),c={x:b.pageX,y:b.pageY},d=this._normValueFromMouse(c),e=this._valueMax()-this._valueMin()+1,this.handles.each(function(b){var c=Math.abs(d-k.values(b));(e>c||e===c&&(b===k._lastChangedValue||k.values(b)===l.min))&&(e=c,f=a(this),g=b)}),h=this._start(b,g),h!==!1&&(this._mouseSliding=!0,this._handleIndex=g,f.addClass("ui-state-active").focus(),i=f.offset(),j=!a(b.target).parents().addBack().is(".ui-slider-handle"),this._clickOffset=j?{left:0,top:0}:{left:b.pageX-i.left-f.width()/2,top:b.pageY-i.top-f.height()/2-(parseInt(f.css("borderTopWidth"),10)||0)-(parseInt(f.css("borderBottomWidth"),10)||0)+(parseInt(f.css("marginTop"),10)||0)},this.handles.hasClass("ui-state-hover")||this._slide(b,g,d),this._animateOff=!0,!0))},_mouseStart:function(){return!0},_mouseDrag:function(a){var b={x:a.pageX,y:a.pageY},c=this._normValueFromMouse(b);return this._slide(a,this._handleIndex,c),!1},_mouseStop:function(a){return this.handles.removeClass("ui-state-active"),this._mouseSliding=!1,this._stop(a,this._handleIndex),this._change(a,this._handleIndex),this._handleIndex=null,this._clickOffset=null,this._animateOff=!1,!1},_detectOrientation:function(){this.orientation="vertical"===this.options.orientation?"vertical":"horizontal"},_normValueFromMouse:function(a){var b,c,d,e,f;return"horizontal"===this.orientation?(b=this.elementSize.width,c=a.x-this.elementOffset.left-(this._clickOffset?this._clickOffset.left:0)):(b=this.elementSize.height,c=a.y-this.elementOffset.top-(this._clickOffset?this._clickOffset.top:0)),d=c/b,d>1&&(d=1),d<0&&(d=0),"vertical"===this.orientation&&(d=1-d),e=this._valueMax()-this._valueMin(),f=this._valueMin()+d*e,this._trimAlignValue(f)},_start:function(a,b){var c={handle:this.handles[b],value:this.value()};return this.options.values&&this.options.values.length&&(c.value=this.values(b),c.values=this.values()),this._trigger("start",a,c)},_slide:function(a,b,c){var d,e,f;this.options.values&&this.options.values.length?(d=this.values(b?0:1),2===this.options.values.length&&this.options.range===!0&&(0===b&&c>d||1===b&&c<d)&&(c=d),c!==this.values(b)&&(e=this.values(),e[b]=c,f=this._trigger("slide",a,{handle:this.handles[b],value:c,values:e}),d=this.values(b?0:1),f!==!1&&this.values(b,c))):c!==this.value()&&(f=this._trigger("slide",a,{handle:this.handles[b],value:c}),f!==!1&&this.value(c))},_stop:function(a,b){var c={handle:this.handles[b],value:this.value()};this.options.values&&this.options.values.length&&(c.value=this.values(b),c.values=this.values()),this._trigger("stop",a,c)},_change:function(a,b){if(!this._keySliding&&!this._mouseSliding){var c={handle:this.handles[b],value:this.value()};this.options.values&&this.options.values.length&&(c.value=this.values(b),c.values=this.values()),this._lastChangedValue=b,this._trigger("change",a,c)}},value:function(a){return arguments.length?(this.options.value=this._trimAlignValue(a),this._refreshValue(),void this._change(null,0)):this._value()},values:function(b,c){var d,e,f;if(arguments.length>1)return this.options.values[b]=this._trimAlignValue(c),this._refreshValue(),void this._change(null,b);if(!arguments.length)return this._values();if(!a.isArray(arguments[0]))return this.options.values&&this.options.values.length?this._values(b):this.value();for(d=this.options.values,e=arguments[0],f=0;f<d.length;f+=1)d[f]=this._trimAlignValue(e[f]),this._change(null,f);this._refreshValue()},_setOption:function(b,c){var d,e=0;switch("range"===b&&this.options.range===!0&&("min"===c?(this.options.value=this._values(0),this.options.values=null):"max"===c&&(this.options.value=this._values(this.options.values.length-1),this.options.values=null)),a.isArray(this.options.values)&&(e=this.options.values.length),"disabled"===b&&this.element.toggleClass("ui-state-disabled",!!c),this._super(b,c),b){case"orientation":this._detectOrientation(),this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-"+this.orientation),this._refreshValue(),this.handles.css("horizontal"===c?"bottom":"left","");break;case"value":this._animateOff=!0,this._refreshValue(),this._change(null,0),this._animateOff=!1;break;case"values":for(this._animateOff=!0,this._refreshValue(),d=0;d<e;d+=1)this._change(null,d);this._animateOff=!1;break;case"step":case"min":case"max":this._animateOff=!0,this._calculateNewMax(),this._refreshValue(),this._animateOff=!1;break;case"range":this._animateOff=!0,this._refresh(),this._animateOff=!1}},_value:function(){var a=this.options.value;return a=this._trimAlignValue(a)},_values:function(a){var b,c,d;if(arguments.length)return b=this.options.values[a],b=this._trimAlignValue(b);if(this.options.values&&this.options.values.length){for(c=this.options.values.slice(),d=0;d<c.length;d+=1)c[d]=this._trimAlignValue(c[d]);return c}return[]},_trimAlignValue:function(a){if(a<=this._valueMin())return this._valueMin();if(a>=this._valueMax())return this._valueMax();var b=this.options.step>0?this.options.step:1,c=(a-this._valueMin())%b,d=a-c;return 2*Math.abs(c)>=b&&(d+=c>0?b:-b),parseFloat(d.toFixed(5))},_calculateNewMax:function(){var a=this.options.max,b=this._valueMin(),c=this.options.step,d=Math.floor(+(a-b).toFixed(this._precision())/c)*c;a=d+b,this.max=parseFloat(a.toFixed(this._precision()))},_precision:function(){var a=this._precisionOf(this.options.step);return null!==this.options.min&&(a=Math.max(a,this._precisionOf(this.options.min))),a},_precisionOf:function(a){var b=a.toString(),c=b.indexOf(".");return c===-1?0:b.length-c-1},_valueMin:function(){return this.options.min},_valueMax:function(){return this.max},_refreshValue:function(){var b,c,d,e,f,g=this.options.range,h=this.options,i=this,j=!this._animateOff&&h.animate,k={};this.options.values&&this.options.values.length?this.handles.each(function(d){c=(i.values(d)-i._valueMin())/(i._valueMax()-i._valueMin())*100,k["horizontal"===i.orientation?"left":"bottom"]=c+"%",a(this).stop(1,1)[j?"animate":"css"](k,h.animate),i.options.range===!0&&("horizontal"===i.orientation?(0===d&&i.range.stop(1,1)[j?"animate":"css"]({left:c+"%"},h.animate),1===d&&i.range[j?"animate":"css"]({width:c-b+"%"},{queue:!1,duration:h.animate})):(0===d&&i.range.stop(1,1)[j?"animate":"css"]({bottom:c+"%"},h.animate),1===d&&i.range[j?"animate":"css"]({height:c-b+"%"},{queue:!1,duration:h.animate}))),b=c}):(d=this.value(),e=this._valueMin(),f=this._valueMax(),c=f!==e?(d-e)/(f-e)*100:0,k["horizontal"===this.orientation?"left":"bottom"]=c+"%",this.handle.stop(1,1)[j?"animate":"css"](k,h.animate),"min"===g&&"horizontal"===this.orientation&&this.range.stop(1,1)[j?"animate":"css"]({width:c+"%"},h.animate),"max"===g&&"horizontal"===this.orientation&&this.range[j?"animate":"css"]({width:100-c+"%"},{queue:!1,duration:h.animate}),"min"===g&&"vertical"===this.orientation&&this.range.stop(1,1)[j?"animate":"css"]({height:c+"%"},h.animate),"max"===g&&"vertical"===this.orientation&&this.range[j?"animate":"css"]({height:100-c+"%"},{queue:!1,duration:h.animate}))},_handleEvents:{keydown:function(b){var c,d,e,f,g=a(b.target).data("ui-slider-handle-index");switch(b.keyCode){case a.ui.keyCode.HOME:case a.ui.keyCode.END:case a.ui.keyCode.PAGE_UP:case a.ui.keyCode.PAGE_DOWN:case a.ui.keyCode.UP:case a.ui.keyCode.RIGHT:case a.ui.keyCode.DOWN:case a.ui.keyCode.LEFT:if(b.preventDefault(),!this._keySliding&&(this._keySliding=!0,a(b.target).addClass("ui-state-active"),c=this._start(b,g),c===!1))return}switch(f=this.options.step,d=e=this.options.values&&this.options.values.length?this.values(g):this.value(),b.keyCode){case a.ui.keyCode.HOME:e=this._valueMin();break;case a.ui.keyCode.END:e=this._valueMax();break;case a.ui.keyCode.PAGE_UP:e=this._trimAlignValue(d+(this._valueMax()-this._valueMin())/this.numPages);break;case a.ui.keyCode.PAGE_DOWN:e=this._trimAlignValue(d-(this._valueMax()-this._valueMin())/this.numPages);break;case a.ui.keyCode.UP:case a.ui.keyCode.RIGHT:if(d===this._valueMax())return;e=this._trimAlignValue(d+f);break;case a.ui.keyCode.DOWN:case a.ui.keyCode.LEFT:if(d===this._valueMin())return;e=this._trimAlignValue(d-f)}this._slide(b,g,e)},keyup:function(b){var c=a(b.target).data("ui-slider-handle-index");this._keySliding&&(this._keySliding=!1,this._stop(b,c),this._change(b,c),a(b.target).removeClass("ui-state-active"))}}})});
/*!
 * jQuery UI Touch Punch 0.2.2
 *
 * Copyright 2011, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
(function(b){b.support.touch="ontouchend" in document;if(!b.support.touch){return}var c=b.ui.mouse.prototype,e=c._mouseInit,a;function d(g,h){if(g.originalEvent.touches.length>1){return}g.preventDefault();var i=g.originalEvent.changedTouches[0],f=document.createEvent("MouseEvents");f.initMouseEvent(h,true,true,window,1,i.screenX,i.screenY,i.clientX,i.clientY,false,false,false,false,0,null);g.target.dispatchEvent(f)}c._touchStart=function(g){var f=this;if(a||!f._mouseCapture(g.originalEvent.changedTouches[0])){return}a=true;f._touchMoved=false;d(g,"mouseover");d(g,"mousemove");d(g,"mousedown")};c._touchMove=function(f){if(!a){return}this._touchMoved=true;d(f,"mousemove")};c._touchEnd=function(f){if(!a){return}d(f,"mouseup");d(f,"mouseout");if(!this._touchMoved){d(f,"click")}a=false};c._mouseInit=function(){var f=this;f.element.bind("touchstart",b.proxy(f,"_touchStart")).bind("touchmove",b.proxy(f,"_touchMove")).bind("touchend",b.proxy(f,"_touchEnd"));e.call(f)}})(jQuery);
/*! Iris Color Picker - v1.1.0-beta - 2016-10-25
* https://github.com/Automattic/Iris
* Copyright (c) 2016 Matt Wiebe; Licensed GPLv2 */
!function(a,b){function c(){var b,c,d="backgroundImage";j?k="filter":(b=a('<div id="iris-gradtest" />'),c="linear-gradient(top,#fff,#000)",a.each(l,function(a,e){if(b.css(d,e+c),b.css(d).match("gradient"))return k=a,!1}),k===!1&&(b.css("background","-webkit-gradient(linear,0% 0%,0% 100%,from(#fff),to(#000))"),b.css(d).match("gradient")&&(k="webkit")),b.remove())}function d(b,c){return b="top"===b?"top":"left",c=a.isArray(c)?c:Array.prototype.slice.call(arguments,1),"webkit"===k?f(b,c):l[k]+"linear-gradient("+b+", "+c.join(", ")+")"}function e(b,c){var d,e,f,h,i,j,k,l,m;b="top"===b?"top":"left",c=a.isArray(c)?c:Array.prototype.slice.call(arguments,1),d="top"===b?0:1,e=a(this),f=c.length-1,h="filter",i=1===d?"left":"top",j=1===d?"right":"bottom",k=1===d?"height":"width",l='<div class="iris-ie-gradient-shim" style="position:absolute;'+k+":100%;"+i+":%start%;"+j+":%end%;"+h+':%filter%;" data-color:"%color%"></div>',m="","static"===e.css("position")&&e.css({position:"relative"}),c=g(c),a.each(c,function(a,b){var e,g,h;return a!==f&&(e=c[a+1],void(b.stop!==e.stop&&(g=100-parseFloat(e.stop)+"%",b.octoHex=new Color(b.color).toIEOctoHex(),e.octoHex=new Color(e.color).toIEOctoHex(),h="progid:DXImageTransform.Microsoft.Gradient(GradientType="+d+", StartColorStr='"+b.octoHex+"', EndColorStr='"+e.octoHex+"')",m+=l.replace("%start%",b.stop).replace("%end%",g).replace("%filter%",h))))}),e.find(".iris-ie-gradient-shim").remove(),a(m).prependTo(e)}function f(b,c){var d=[];return b="top"===b?"0% 0%,0% 100%,":"0% 100%,100% 100%,",c=g(c),a.each(c,function(a,b){d.push("color-stop("+parseFloat(b.stop)/100+", "+b.color+")")}),"-webkit-gradient(linear,"+b+d.join(",")+")"}function g(b){var c=[],d=[],e=[],f=b.length-1;return a.each(b,function(a,b){var e=b,f=!1,g=b.match(/1?[0-9]{1,2}%$/);g&&(e=b.replace(/\s?1?[0-9]{1,2}%$/,""),f=g.shift()),c.push(e),d.push(f)}),d[0]===!1&&(d[0]="0%"),d[f]===!1&&(d[f]="100%"),d=h(d),a.each(d,function(a){e[a]={color:c[a],stop:d[a]}}),e}function h(b){var c,d,e,f,g=0,i=b.length-1,j=0,k=!1;if(b.length<=2||a.inArray(!1,b)<0)return b;for(;j<b.length-1;)k||b[j]!==!1?k&&b[j]!==!1&&(i=j,j=b.length):(g=j-1,k=!0),j++;for(d=i-g,f=parseInt(b[g].replace("%"),10),c=(parseFloat(b[i].replace("%"))-f)/d,j=g+1,e=1;j<i;)b[j]=f+e*c+"%",e++,j++;return h(b)}var i,j,k,l,m,n,o,p,q;return i='<div class="iris-picker"><div class="iris-picker-inner"><div class="iris-square"><a class="iris-square-value" href="#"><span class="iris-square-handle ui-slider-handle"></span></a><div class="iris-square-inner iris-square-horiz"></div><div class="iris-square-inner iris-square-vert"></div></div><div class="iris-slider iris-strip"><div class="iris-slider-offset"></div></div></div></div>',m='.iris-picker{display:block;position:relative}.iris-picker,.iris-picker *{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input+.iris-picker{margin-top:4px}.iris-error{background-color:#ffafaf}.iris-border{border-radius:3px;border:1px solid #aaa;width:200px;background-color:#fff}.iris-picker-inner{position:absolute;top:0;right:0;left:0;bottom:0}.iris-border .iris-picker-inner{top:10px;right:10px;left:10px;bottom:10px}.iris-picker .iris-square-inner{position:absolute;left:0;right:0;top:0;bottom:0}.iris-picker .iris-square,.iris-picker .iris-slider,.iris-picker .iris-square-inner,.iris-picker .iris-palette{border-radius:3px;box-shadow:inset 0 0 5px rgba(0,0,0,.4);height:100%;width:12.5%;float:left;margin-right:5%}.iris-only-strip .iris-slider{width:100%}.iris-picker .iris-square{width:76%;margin-right:10%;position:relative}.iris-only-strip .iris-square{display:none}.iris-picker .iris-square-inner{width:auto;margin:0}.iris-ie-9 .iris-square,.iris-ie-9 .iris-slider,.iris-ie-9 .iris-square-inner,.iris-ie-9 .iris-palette{box-shadow:none;border-radius:0}.iris-ie-9 .iris-square,.iris-ie-9 .iris-slider,.iris-ie-9 .iris-palette{outline:1px solid rgba(0,0,0,.1)}.iris-ie-lt9 .iris-square,.iris-ie-lt9 .iris-slider,.iris-ie-lt9 .iris-square-inner,.iris-ie-lt9 .iris-palette{outline:1px solid #aaa}.iris-ie-lt9 .iris-square .ui-slider-handle{outline:1px solid #aaa;background-color:#fff;-ms-filter:"alpha(Opacity=30)"}.iris-ie-lt9 .iris-square .iris-square-handle{background:0 0;border:3px solid #fff;-ms-filter:"alpha(Opacity=50)"}.iris-picker .iris-strip{margin-right:0;position:relative}.iris-picker .iris-strip .ui-slider-handle{position:absolute;background:0 0;margin:0;right:-3px;left:-3px;border:4px solid #aaa;border-width:4px 3px;width:auto;height:6px;border-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,.2);opacity:.9;z-index:5;cursor:ns-resize}.iris-strip-horiz .iris-strip .ui-slider-handle{right:auto;left:auto;bottom:-3px;top:-3px;height:auto;width:6px;cursor:ew-resize}.iris-strip .ui-slider-handle:before{content:" ";position:absolute;left:-2px;right:-2px;top:-3px;bottom:-3px;border:2px solid #fff;border-radius:3px}.iris-picker .iris-slider-offset{position:absolute;top:11px;left:0;right:0;bottom:-3px;width:auto;height:auto;background:transparent;border:0;border-radius:0}.iris-strip-horiz .iris-slider-offset{top:0;bottom:0;right:11px;left:-3px}.iris-picker .iris-square-handle{background:transparent;border:5px solid #aaa;border-radius:50%;border-color:rgba(128,128,128,.5);box-shadow:none;width:12px;height:12px;position:absolute;left:-10px;top:-10px;cursor:move;opacity:1;z-index:10}.iris-picker .ui-state-focus .iris-square-handle{opacity:.8}.iris-picker .iris-square-handle:hover{border-color:#999}.iris-picker .iris-square-value:focus .iris-square-handle{box-shadow:0 0 2px rgba(0,0,0,.75);opacity:.8}.iris-picker .iris-square-handle:hover::after{border-color:#fff}.iris-picker .iris-square-handle::after{position:absolute;bottom:-4px;right:-4px;left:-4px;top:-4px;border:3px solid #f9f9f9;border-color:rgba(255,255,255,.8);border-radius:50%;content:" "}.iris-picker .iris-square-value{width:8px;height:8px;position:absolute}.iris-ie-lt9 .iris-square-value,.iris-mozilla .iris-square-value{width:1px;height:1px}.iris-palette-container{position:absolute;bottom:0;left:0;margin:0;padding:0}.iris-border .iris-palette-container{left:10px;bottom:10px}.iris-picker .iris-palette{margin:0;cursor:pointer}.iris-square-handle,.ui-slider-handle{border:0;outline:0}',o=navigator.userAgent.toLowerCase(),p="Microsoft Internet Explorer"===navigator.appName,q=p?parseFloat(o.match(/msie ([0-9]{1,}[\.0-9]{0,})/)[1]):0,j=p&&q<10,k=!1,l=["-moz-","-webkit-","-o-","-ms-"],j&&q<=7?(a.fn.iris=a.noop,void(a.support.iris=!1)):(a.support.iris=!0,a.fn.gradient=function(){var b=arguments;return this.each(function(){j?e.apply(this,b):a(this).css("backgroundImage",d.apply(this,b))})},a.fn.rainbowGradient=function(b,c){var d,e,f,g;for(b=b||"top",d=a.extend({},{s:100,l:50},c),e="hsl(%h%,"+d.s+"%,"+d.l+"%)",f=0,g=[];f<=360;)g.push(e.replace("%h%",f)),f+=30;return this.each(function(){a(this).gradient(b,g)})},n={options:{color:!1,mode:"hsl",controls:{horiz:"s",vert:"l",strip:"h"},hide:!0,border:!0,target:!1,width:200,palettes:!1,type:"full",slider:"horizontal"},_color:"",_palettes:["#000","#fff","#d33","#d93","#ee2","#81d742","#1e73be","#8224e3"],_inited:!1,_defaultHSLControls:{horiz:"s",vert:"l",strip:"h"},_defaultHSVControls:{horiz:"h",vert:"v",strip:"s"},_scale:{h:360,s:100,l:100,v:100},_create:function(){var b=this,d=b.element,e=b.options.color||d.val();k===!1&&c(),d.is("input")?(b.options.target?b.picker=a(i).appendTo(b.options.target):b.picker=a(i).insertAfter(d),b._addInputListeners(d)):(d.append(i),b.picker=d.find(".iris-picker")),p?9===q?b.picker.addClass("iris-ie-9"):q<=8&&b.picker.addClass("iris-ie-lt9"):o.indexOf("compatible")<0&&o.indexOf("khtml")<0&&o.match(/mozilla/)&&b.picker.addClass("iris-mozilla"),b.options.palettes&&b._addPalettes(),b.onlySlider="hue"===b.options.type,b.horizontalSlider=b.onlySlider&&"horizontal"===b.options.slider,b.onlySlider&&(b.options.controls.strip="h",e||(e="hsl(10,100,50)")),b._color=new Color(e).setHSpace(b.options.mode),b.options.color=b._color.toString(),b.controls={square:b.picker.find(".iris-square"),squareDrag:b.picker.find(".iris-square-value"),horiz:b.picker.find(".iris-square-horiz"),vert:b.picker.find(".iris-square-vert"),strip:b.picker.find(".iris-strip"),stripSlider:b.picker.find(".iris-strip .iris-slider-offset")},"hsv"===b.options.mode&&b._has("l",b.options.controls)?b.options.controls=b._defaultHSVControls:"hsl"===b.options.mode&&b._has("v",b.options.controls)&&(b.options.controls=b._defaultHSLControls),b.hue=b._color.h(),b.options.hide&&b.picker.hide(),b.options.border&&!b.onlySlider&&b.picker.addClass("iris-border"),b._initControls(),b.active="external",b._dimensions(),b._change()},_has:function(b,c){var d=!1;return a.each(c,function(a,c){if(b===c)return d=!0,!1}),d},_addPalettes:function(){var b=a('<div class="iris-palette-container" />'),c=a('<a class="iris-palette" tabindex="0" />'),d=a.isArray(this.options.palettes)?this.options.palettes:this._palettes;this.picker.find(".iris-palette-container").length&&(b=this.picker.find(".iris-palette-container").detach().html("")),a.each(d,function(a,d){c.clone().data("color",d).css("backgroundColor",d).appendTo(b).height(10).width(10)}),this.picker.append(b)},_paint:function(){var a=this;a.horizontalSlider?a._paintDimension("left","strip"):a._paintDimension("top","strip"),a._paintDimension("top","vert"),a._paintDimension("left","horiz")},_paintDimension:function(a,b){var c,d=this,e=d._color,f=d.options.mode,g=d._getHSpaceColor(),h=d.controls[b],i=d.options.controls;if(b!==d.active&&("square"!==d.active||"strip"===b))switch(i[b]){case"h":if("hsv"===f){switch(g=e.clone(),b){case"horiz":g[i.vert](100);break;case"vert":g[i.horiz](100);break;case"strip":g.setHSpace("hsl")}c=g.toHsl()}else c="strip"===b?{s:g.s,l:g.l}:{s:100,l:g.l};h.rainbowGradient(a,c);break;case"s":"hsv"===f?"vert"===b?c=[e.clone().a(0).s(0).toCSS("rgba"),e.clone().a(1).s(0).toCSS("rgba")]:"strip"===b?c=[e.clone().s(100).toCSS("hsl"),e.clone().s(0).toCSS("hsl")]:"horiz"===b&&(c=["#fff","hsl("+g.h+",100%,50%)"]):c="vert"===b&&"h"===d.options.controls.horiz?["hsla(0, 0%, "+g.l+"%, 0)","hsla(0, 0%, "+g.l+"%, 1)"]:["hsl("+g.h+",0%,50%)","hsl("+g.h+",100%,50%)"],h.gradient(a,c);break;case"l":c="strip"===b?["hsl("+g.h+",100%,100%)","hsl("+g.h+", "+g.s+"%,50%)","hsl("+g.h+",100%,0%)"]:["#fff","rgba(255,255,255,0) 50%","rgba(0,0,0,0) 50%","rgba(0,0,0,1)"],h.gradient(a,c);break;case"v":c="strip"===b?[e.clone().v(100).toCSS(),e.clone().v(0).toCSS()]:["rgba(0,0,0,0)","#000"],h.gradient(a,c)}},_getHSpaceColor:function(){return"hsv"===this.options.mode?this._color.toHsv():this._color.toHsl()},_stripOnlyDimensions:function(){var a=this,b=this.options.width,c=.12*b;a.horizontalSlider?a.picker.css({width:b,height:c}).addClass("iris-only-strip iris-strip-horiz"):a.picker.css({width:c,height:b}).addClass("iris-only-strip iris-strip-vert")},_dimensions:function(b){if("hue"===this.options.type)return this._stripOnlyDimensions();var c,d,e,f,g=this,h=g.options,i=g.controls,j=i.square,k=g.picker.find(".iris-strip"),l="77.5%",m="12%",n=20,o=h.border?h.width-n:h.width,p=a.isArray(h.palettes)?h.palettes.length:g._palettes.length;return b&&(j.css("width",""),k.css("width",""),g.picker.css({width:"",height:""})),l=o*(parseFloat(l)/100),m=o*(parseFloat(m)/100),c=h.border?l+n:l,j.width(l).height(l),k.height(l).width(m),g.picker.css({width:h.width,height:c}),h.palettes?(d=2*l/100,f=l-(p-1)*d,e=f/p,g.picker.find(".iris-palette").each(function(b){var c=0===b?0:d;a(this).css({width:e,height:e,marginLeft:c})}),g.picker.css("paddingBottom",e+d),void k.height(e+d+l)):g.picker.css("paddingBottom","")},_addInputListeners:function(a){var b=this,c=100,d=function(c){var d=new Color(a.val()),e=a.val().replace(/^#/,"");a.removeClass("iris-error"),d.error?""!==e&&a.addClass("iris-error"):d.toString()!==b._color.toString()&&("keyup"===c.type&&e.match(/^[0-9a-fA-F]{3}$/)||b._setOption("color",d.toString()))};a.on("change",d).on("keyup",b._debounce(d,c)),b.options.hide&&a.one("focus",function(){b.show()})},_initControls:function(){var b=this,c=b.controls,d=c.square,e=b.options.controls,f=b._scale[e.strip],g=b.horizontalSlider?"horizontal":"vertical";c.stripSlider.slider({orientation:g,max:f,slide:function(a,c){b.active="strip","h"===e.strip&&"vertical"===g&&(c.value=f-c.value),b._color[e.strip](c.value),b._change.apply(b,arguments)}}),c.squareDrag.draggable({containment:c.square.find(".iris-square-inner"),zIndex:1e3,cursor:"move",drag:function(a,c){b._squareDrag(a,c)},start:function(){d.addClass("iris-dragging"),a(this).addClass("ui-state-focus")},stop:function(){d.removeClass("iris-dragging"),a(this).removeClass("ui-state-focus")}}).on("mousedown mouseup",function(c){var d="ui-state-focus";c.preventDefault(),"mousedown"===c.type?(b.picker.find("."+d).removeClass(d).blur(),a(this).addClass(d).focus()):a(this).removeClass(d)}).on("keydown",function(a){var d=c.square,e=c.squareDrag,f=e.position(),g=b.options.width/100;switch(a.altKey&&(g*=10),a.keyCode){case 37:f.left-=g;break;case 38:f.top-=g;break;case 39:f.left+=g;break;case 40:f.top+=g;break;default:return!0}f.left=Math.max(0,Math.min(f.left,d.width())),f.top=Math.max(0,Math.min(f.top,d.height())),e.css(f),b._squareDrag(a,{position:f}),a.preventDefault()}),d.mousedown(function(c){var d,e;1===c.which&&a(c.target).is("div")&&(d=b.controls.square.offset(),e={top:c.pageY-d.top,left:c.pageX-d.left},c.preventDefault(),b._squareDrag(c,{position:e}),c.target=b.controls.squareDrag.get(0),b.controls.squareDrag.css(e).trigger(c))}),b.options.palettes&&b._paletteListeners()},_paletteListeners:function(){var b=this;b.picker.find(".iris-palette-container").on("click.palette",".iris-palette",function(){b._color.fromCSS(a(this).data("color")),b.active="external",b._change()}).on("keydown.palette",".iris-palette",function(b){return 13!==b.keyCode&&32!==b.keyCode||(b.stopPropagation(),void a(this).click())})},_squareDrag:function(a,b){var c=this,d=c.options.controls,e=c._squareDimensions(),f=Math.round((e.h-b.position.top)/e.h*c._scale[d.vert]),g=c._scale[d.horiz]-Math.round((e.w-b.position.left)/e.w*c._scale[d.horiz]);c._color[d.horiz](g)[d.vert](f),c.active="square",c._change.apply(c,arguments)},_setOption:function(b,c){var d,e,f,g=this,h=g.options[b],i=!1;switch(g.options[b]=c,b){case"color":g.onlySlider?(c=parseInt(c,10),c=isNaN(c)||c<0||c>359?h:"hsl("+c+",100,50)",g.options.color=g.options[b]=c,g._color=new Color(c).setHSpace(g.options.mode),g.active="external",g._change()):(c=""+c,d=c.replace(/^#/,""),e=new Color(c).setHSpace(g.options.mode),e.error?g.options[b]=h:(g._color=e,g.options.color=g.options[b]=g._color.toString(),g.active="external",g._change()));break;case"palettes":i=!0,c?g._addPalettes():g.picker.find(".iris-palette-container").remove(),h||g._paletteListeners();break;case"width":i=!0;break;case"border":i=!0,f=c?"addClass":"removeClass",g.picker[f]("iris-border");break;case"mode":case"controls":if(h===c)return;return f=g.element,h=g.options,h.hide=!g.picker.is(":visible"),g.destroy(),g.picker.remove(),a(g.element).iris(h)}i&&g._dimensions(!0)},_squareDimensions:function(a){var c,d,e=this.controls.square;return a!==b&&e.data("dimensions")?e.data("dimensions"):(d=this.controls.squareDrag,c={w:e.width(),h:e.height()},e.data("dimensions",c),c)},_isNonHueControl:function(a,b){return"square"===a&&"h"===this.options.controls.strip||"external"!==b&&("h"!==b||"strip"!==a)},_change:function(){var b=this,c=b.controls,d=b._getHSpaceColor(),e=["square","strip"],f=b.options.controls,g=f[b.active]||"external",h=b.hue;"strip"===b.active?e=[]:"external"!==b.active&&e.pop(),a.each(e,function(a,e){var g,h,i;if(e!==b.active)switch(e){case"strip":g="h"!==f.strip||b.horizontalSlider?d[f.strip]:b._scale[f.strip]-d[f.strip],c.stripSlider.slider("value",g);break;case"square":h=b._squareDimensions(),i={left:d[f.horiz]/b._scale[f.horiz]*h.w,top:h.h-d[f.vert]/b._scale[f.vert]*h.h},b.controls.squareDrag.css(i)}}),d.h!==h&&b._isNonHueControl(b.active,g)&&b._color.h(h),b.hue=b._color.h(),b.options.color=b._color.toString(),b._inited&&b._trigger("change",{type:b.active},{color:b._color}),b.element.is(":input")&&!b._color.error&&(b.element.removeClass("iris-error"),b.onlySlider?b.element.val()!==b.hue&&b.element.val(b.hue):b.element.val()!==b._color.toString()&&b.element.val(b._color.toString())),b._paint(),b._inited=!0,b.active=!1},_debounce:function(a,b,c){var d,e;return function(){var f,g,h=this,i=arguments;return f=function(){d=null,c||(e=a.apply(h,i))},g=c&&!d,clearTimeout(d),d=setTimeout(f,b),g&&(e=a.apply(h,i)),e}},show:function(){this.picker.show()},hide:function(){this.picker.hide()},toggle:function(){this.picker.toggle()},color:function(a){return a===!0?this._color.clone():a===b?this._color.toString():void this.option("color",a)}},a.widget("a8c.iris",n),void a('<style id="iris-css">'+m+"</style>").appendTo("head"))}(jQuery),function(a,b){var c=function(a,b){return this instanceof c?this._init(a,b):new c(a,b)};c.fn=c.prototype={_color:0,_alpha:1,error:!1,_hsl:{h:0,s:0,l:0},_hsv:{h:0,s:0,v:0},_hSpace:"hsl",_init:function(a){var c="noop";switch(typeof a){case"object":return a.a!==b&&this.a(a.a),c=a.r!==b?"fromRgb":a.l!==b?"fromHsl":a.v!==b?"fromHsv":c,this[c](a);case"string":return this.fromCSS(a);case"number":return this.fromInt(parseInt(a,10))}return this},_error:function(){return this.error=!0,this},clone:function(){for(var a=new c(this.toInt()),b=["_alpha","_hSpace","_hsl","_hsv","error"],d=b.length-1;d>=0;d--)a[b[d]]=this[b[d]];return a},setHSpace:function(a){return this._hSpace="hsv"===a?a:"hsl",this},noop:function(){return this},fromCSS:function(a){var b,c=/^(rgb|hs(l|v))a?\(/;if(this.error=!1,a=a.replace(/^\s+/,"").replace(/\s+$/,"").replace(/;$/,""),a.match(c)&&a.match(/\)$/)){if(b=a.replace(/(\s|%)/g,"").replace(c,"").replace(/,?\);?$/,"").split(","),b.length<3)return this._error();if(4===b.length&&(this.a(parseFloat(b.pop())),this.error))return this;for(var d=b.length-1;d>=0;d--)if(b[d]=parseInt(b[d],10),isNaN(b[d]))return this._error();return a.match(/^rgb/)?this.fromRgb({r:b[0],g:b[1],b:b[2]}):a.match(/^hsv/)?this.fromHsv({h:b[0],s:b[1],v:b[2]}):this.fromHsl({h:b[0],s:b[1],l:b[2]})}return this.fromHex(a)},fromRgb:function(a,c){return"object"!=typeof a||a.r===b||a.g===b||a.b===b?this._error():(this.error=!1,this.fromInt(parseInt((a.r<<16)+(a.g<<8)+a.b,10),c))},fromHex:function(a){return a=a.replace(/^#/,"").replace(/^0x/,""),3===a.length&&(a=a[0]+a[0]+a[1]+a[1]+a[2]+a[2]),this.error=!/^[0-9A-F]{6}$/i.test(a),this.fromInt(parseInt(a,16))},fromHsl:function(a){var c,d,e,f,g,h,i,j;return"object"!=typeof a||a.h===b||a.s===b||a.l===b?this._error():(this._hsl=a,this._hSpace="hsl",h=a.h/360,i=a.s/100,j=a.l/100,0===i?c=d=e=j:(f=j<.5?j*(1+i):j+i-j*i,g=2*j-f,c=this.hue2rgb(g,f,h+1/3),d=this.hue2rgb(g,f,h),e=this.hue2rgb(g,f,h-1/3)),this.fromRgb({r:255*c,g:255*d,b:255*e},!0))},fromHsv:function(a){var c,d,e,f,g,h,i,j,k,l,m;if("object"!=typeof a||a.h===b||a.s===b||a.v===b)return this._error();switch(this._hsv=a,this._hSpace="hsv",c=a.h/360,d=a.s/100,e=a.v/100,i=Math.floor(6*c),j=6*c-i,k=e*(1-d),l=e*(1-j*d),m=e*(1-(1-j)*d),i%6){case 0:f=e,g=m,h=k;break;case 1:f=l,g=e,h=k;break;case 2:f=k,g=e,h=m;break;case 3:f=k,g=l,h=e;break;case 4:f=m,g=k,h=e;break;case 5:f=e,g=k,h=l}return this.fromRgb({r:255*f,g:255*g,b:255*h},!0)},fromInt:function(a,c){return this._color=parseInt(a,10),isNaN(this._color)&&(this._color=0),this._color>16777215?this._color=16777215:this._color<0&&(this._color=0),c===b&&(this._hsv.h=this._hsv.s=this._hsl.h=this._hsl.s=0),this},hue2rgb:function(a,b,c){return c<0&&(c+=1),c>1&&(c-=1),c<1/6?a+6*(b-a)*c:c<.5?b:c<2/3?a+(b-a)*(2/3-c)*6:a},toString:function(){var a=parseInt(this._color,10).toString(16);if(this.error)return"";if(a.length<6)for(var b=6-a.length-1;b>=0;b--)a="0"+a;return"#"+a},toCSS:function(a,b){switch(a=a||"hex",b=parseFloat(b||this._alpha),a){case"rgb":case"rgba":var c=this.toRgb();return b<1?"rgba( "+c.r+", "+c.g+", "+c.b+", "+b+" )":"rgb( "+c.r+", "+c.g+", "+c.b+" )";case"hsl":case"hsla":var d=this.toHsl();return b<1?"hsla( "+d.h+", "+d.s+"%, "+d.l+"%, "+b+" )":"hsl( "+d.h+", "+d.s+"%, "+d.l+"% )";default:return this.toString()}},toRgb:function(){return{r:255&this._color>>16,g:255&this._color>>8,b:255&this._color}},toHsl:function(){var a,b,c=this.toRgb(),d=c.r/255,e=c.g/255,f=c.b/255,g=Math.max(d,e,f),h=Math.min(d,e,f),i=(g+h)/2;if(g===h)a=b=0;else{var j=g-h;switch(b=i>.5?j/(2-g-h):j/(g+h),g){case d:a=(e-f)/j+(e<f?6:0);break;case e:a=(f-d)/j+2;break;case f:a=(d-e)/j+4}a/=6}return a=Math.round(360*a),0===a&&this._hsl.h!==a&&(a=this._hsl.h),b=Math.round(100*b),0===b&&this._hsl.s&&(b=this._hsl.s),{h:a,s:b,l:Math.round(100*i)}},toHsv:function(){var a,b,c=this.toRgb(),d=c.r/255,e=c.g/255,f=c.b/255,g=Math.max(d,e,f),h=Math.min(d,e,f),i=g,j=g-h;if(b=0===g?0:j/g,g===h)a=b=0;else{switch(g){case d:a=(e-f)/j+(e<f?6:0);break;case e:a=(f-d)/j+2;break;case f:a=(d-e)/j+4}a/=6}return a=Math.round(360*a),0===a&&this._hsv.h!==a&&(a=this._hsv.h),b=Math.round(100*b),0===b&&this._hsv.s&&(b=this._hsv.s),{h:a,s:b,v:Math.round(100*i)}},toInt:function(){return this._color},toIEOctoHex:function(){var a=this.toString(),b=parseInt(255*this._alpha,10).toString(16);return 1===b.length&&(b="0"+b),"#"+b+a.replace(/^#/,"")},toLuminosity:function(){var a=this.toRgb(),b={};for(var c in a)if(a.hasOwnProperty(c)){var d=a[c]/255;b[c]=d<=.03928?d/12.92:Math.pow((d+.055)/1.055,2.4)}return.2126*b.r+.7152*b.g+.0722*b.b},getDistanceLuminosityFrom:function(a){if(!(a instanceof c))throw"getDistanceLuminosityFrom requires a Color object";var b=this.toLuminosity(),d=a.toLuminosity();return b>d?(b+.05)/(d+.05):(d+.05)/(b+.05)},getMaxContrastColor:function(){var a=this.getDistanceLuminosityFrom(new c("#000")),b=this.getDistanceLuminosityFrom(new c("#fff")),d=a>=b?"#000":"#fff";return new c(d)},getReadableContrastingColor:function(a,d){if(!(a instanceof c))return this;var e,f,g,h=d===b?5:d,i=a.getDistanceLuminosityFrom(this);if(i>=h)return this;if(e=a.getMaxContrastColor(),f=e.getDistanceLuminosityFrom(a),f<=h)return e;for(g=0===e.toInt()?-1:1;i<h&&(this.l(g,!0),i=this.getDistanceLuminosityFrom(a),0!==this._color&&16777215!==this._color););return this},a:function(a){if(a===b)return this._alpha;var c=parseFloat(a);return isNaN(c)?this._error():(this._alpha=c,this)},darken:function(a){return a=a||5,this.l(-a,!0)},lighten:function(a){return a=a||5,this.l(a,!0)},saturate:function(a){return a=a||15,this.s(a,!0)},desaturate:function(a){return a=a||15,this.s(-a,!0)},toGrayscale:function(){return this.setHSpace("hsl").s(0)},getComplement:function(){return this.h(180,!0)},getSplitComplement:function(a){a=a||1;var b=180+30*a;return this.h(b,!0)},getAnalog:function(a){a=a||1;var b=30*a;return this.h(b,!0)},getTetrad:function(a){a=a||1;var b=60*a;return this.h(b,!0)},getTriad:function(a){a=a||1;var b=120*a;return this.h(b,!0)},_partial:function(a){var c=d[a];return function(d,e){var f=this._spaceFunc("to",c.space);return d===b?f[a]:(e===!0&&(d=f[a]+d),c.mod&&(d%=c.mod),c.range&&(d=d<c.range[0]?c.range[0]:d>c.range[1]?c.range[1]:d),f[a]=d,this._spaceFunc("from",c.space,f))}},_spaceFunc:function(a,b,c){var d=b||this._hSpace,e=a+d.charAt(0).toUpperCase()+d.substr(1);return this[e](c)}};var d={h:{mod:360},s:{range:[0,100]},l:{space:"hsl",range:[0,100]},v:{space:"hsv",range:[0,100]},r:{space:"rgb",range:[0,255]},g:{space:"rgb",range:[0,255]},b:{space:"rgb",range:[0,255]}};for(var e in d)d.hasOwnProperty(e)&&(c.fn[e]=c.fn._partial(e));"object"==typeof exports?module.exports=c:a.Color=c}(this);
!function(a,b){var c,d='<button type="button" class="button wp-color-result" aria-expanded="false"><span class="wp-color-result-text"></span></button>',e='<div class="wp-picker-holder" />',f='<div class="wp-picker-container" />',g='<input type="button" class="button button-small" />',h="<label></label>",i='<span class="screen-reader-text"></span>';c={options:{defaultColor:!1,change:!1,clear:!1,hide:!0,palettes:!0,width:255,mode:"hsv",type:"full",slider:"horizontal"},_createHueOnly:function(){var b,c=this,d=c.element;d.hide(),b="hsl("+d.val()+", 100, 50)",d.iris({mode:"hsl",type:"hue",hide:!1,color:b,change:function(b,d){a.isFunction(c.options.change)&&c.options.change.call(this,b,d)},width:c.options.width,slider:c.options.slider})},_create:function(){if(a.support.iris){var b=this,c=b.element;if(a.extend(b.options,c.data()),"hue"===b.options.type)return b._createHueOnly();b.close=a.proxy(b.close,b),b.initialValue=c.val(),c.addClass("wp-color-picker"),c.parent("label").length||(c.wrap(h),b.wrappingLabelText=a(i).insertBefore(c).text(wpColorPickerL10n.defaultLabel)),b.wrappingLabel=c.parent(),b.wrappingLabel.wrap(f),b.wrap=b.wrappingLabel.parent(),b.toggler=a(d).insertBefore(b.wrappingLabel).css({backgroundColor:b.initialValue}),b.toggler.find(".wp-color-result-text").text(wpColorPickerL10n.pick),b.pickerContainer=a(e).insertAfter(b.wrappingLabel),b.button=a(g),b.options.defaultColor?b.button.addClass("wp-picker-default").val(wpColorPickerL10n.defaultString).attr("aria-label",wpColorPickerL10n.defaultAriaLabel):b.button.addClass("wp-picker-clear").val(wpColorPickerL10n.clear).attr("aria-label",wpColorPickerL10n.clearAriaLabel),b.wrappingLabel.wrap('<span class="wp-picker-input-wrap hidden" />').after(b.button),b.inputWrapper=c.closest(".wp-picker-input-wrap"),c.iris({target:b.pickerContainer,hide:b.options.hide,width:b.options.width,mode:b.options.mode,palettes:b.options.palettes,change:function(c,d){b.toggler.css({backgroundColor:d.color.toString()}),a.isFunction(b.options.change)&&b.options.change.call(this,c,d)}}),c.val(b.initialValue),b._addListeners(),b.options.hide||b.toggler.click()}},_addListeners:function(){var b=this;b.wrap.on("click.wpcolorpicker",function(a){a.stopPropagation()}),b.toggler.click(function(){b.toggler.hasClass("wp-picker-open")?b.close():b.open()}),b.element.change(function(c){var d=a(this),e=d.val();""!==e&&"#"!==e||(b.toggler.css("backgroundColor",""),a.isFunction(b.options.clear)&&b.options.clear.call(this,c))}),b.button.click(function(c){var d=a(this);d.hasClass("wp-picker-clear")?(b.element.val(""),b.toggler.css("backgroundColor",""),a.isFunction(b.options.clear)&&b.options.clear.call(this,c)):d.hasClass("wp-picker-default")&&b.element.val(b.options.defaultColor).change()})},open:function(){this.element.iris("toggle"),this.inputWrapper.removeClass("hidden"),this.wrap.addClass("wp-picker-active"),this.toggler.addClass("wp-picker-open").attr("aria-expanded","true"),a("body").trigger("click.wpcolorpicker").on("click.wpcolorpicker",this.close)},close:function(){this.element.iris("toggle"),this.inputWrapper.addClass("hidden"),this.wrap.removeClass("wp-picker-active"),this.toggler.removeClass("wp-picker-open").attr("aria-expanded","false"),a("body").off("click.wpcolorpicker",this.close)},color:function(a){return a===b?this.element.iris("option","color"):void this.element.iris("option","color",a)},defaultColor:function(a){return a===b?this.options.defaultColor:void(this.options.defaultColor=a)}},a.widget("wp.wpColorPicker",c)}(jQuery);
window.wp=window.wp||{},function(a,b){function c(){function c(){!w&&window.tinymce&&(w=window.tinymce,x=w.$,x(document).on("click",function(a){var b,c,d=x(a.target);d.hasClass("wp-switch-editor")&&(b=d.attr("data-wp-editor-id"),c=d.hasClass("switch-tmce")?"tmce":"html",e(b,c))}))}function d(a){var b=x(".mce-toolbar-grp",a.getContainer())[0],c=b&&b.clientHeight;return c&&c>10&&c<200?parseInt(c,10):30}function e(a,b){a=a||"content",b=b||"toggle";var c,e,f,g=w.get(a),h=x("#wp-"+a+"-wrap"),i=x("#"+a),j=i[0];if("toggle"===b&&(b=g&&!g.isHidden()?"html":"tmce"),"tmce"===b||"tinymce"===b){if(g&&!g.isHidden())return!1;"undefined"!=typeof window.QTags&&window.QTags.closeAllTags(a),c=parseInt(j.style.height,10)||0;var k=!1;k=g?g.getParam("wp_keep_scroll_position"):window.tinyMCEPreInit.mceInit[a]&&window.tinyMCEPreInit.mceInit[a].wp_keep_scroll_position,k&&l(i),g?(g.show(),!w.Env.iOS&&c&&(e=d(g),c=c-e+14,c>50&&c<5e3&&g.theme.resizeTo(null,c)),g.getParam("wp_keep_scroll_position")&&m(g)):w.init(window.tinyMCEPreInit.mceInit[a]),h.removeClass("html-active").addClass("tmce-active"),i.attr("aria-hidden",!0),window.setUserSetting("editor","tinymce")}else if("html"===b){if(g&&g.isHidden())return!1;if(g){w.Env.iOS||(f=g.iframeElement,c=f?parseInt(f.style.height,10):0,c&&(e=d(g),c=c+e-14,c>50&&c<5e3&&(j.style.height=c+"px")));var n=null;g.getParam("wp_keep_scroll_position")&&(n=q(g)),g.hide(),n&&r(g,n)}else i.css({display:"",visibility:""});h.removeClass("tmce-active").addClass("html-active"),i.attr("aria-hidden",!1),window.setUserSetting("editor","html")}}function f(a,b){var c=a.lastIndexOf("<",b-1),d=a.lastIndexOf(">",b);if(c>d||">"===a.substr(b,1)){var e=a.substr(c),f=e.match(/<\s*(\/)?(\w+)/);if(!f)return null;var g=f[2],h=e.indexOf(">");return{ltPos:c,gtPos:c+h+1,tagType:g,isClosingTag:!!f[1]}}return null}function g(a,b){for(var c=i(a),d=0;d<c.length;d++){var e=c[d];if(b>=e.startIndex&&b<=e.endIndex)return e}}function h(a){var b=a.match(/\[+([\w_-])+/g),c=[];if(b)for(var d=0;d<b.length;d++){var e=b[d].replace(/^\[+/g,"");c.indexOf(e)===-1&&c.push(e)}return c}function i(a){var c,d=h(a);if(0===d.length)return[];for(var e,f=b.shortcode.regexp(d.join("|")),g=[];e=f.exec(a);){var i="["===e[1];c={shortcodeName:e[2],showAsPlainText:i,startIndex:e.index,endIndex:e.index+e[0].length,length:e[0].length},g.push(c)}for(var j=new RegExp('(^|[\\n\\r][\\n\\r]|<p>)(https?:\\/\\/[^s"]+?)(<\\/p>s*|[\\n\\r][\\n\\r]|$)',"gi");e=j.exec(a);)c={shortcodeName:"url",showAsPlainText:!1,startIndex:e.index,endIndex:e.index+e[0].length,length:e[0].length,urlAtStartOfContent:""===e[1],urlAtEndOfContent:""===e[3]},g.push(c);return g}function j(a,b){return a("<span>").css({display:"inline-block",width:0,overflow:"hidden","line-height":0}).html(b?b:"")}function k(a,b){var c=["area","base","br","col","embed","hr","img","input","keygen","link","meta","param","source","track","wbr"],d=b.cursorStart,e=b.cursorEnd,h=f(a,d);h&&(d=c.indexOf(h.tagType)!==-1?h.ltPos:h.gtPos);var i=f(a,e);i&&(e=i.gtPos);var j=g(a,d);j&&!j.showAsPlainText&&(d=j.urlAtStartOfContent?j.endIndex:j.startIndex);var k=g(a,e);return k&&!k.showAsPlainText&&(e=k.urlAtEndOfContent?k.startIndex:k.endIndex),{cursorStart:d,cursorEnd:e}}function l(a){if(a&&a.length){var b=a[0],c=b.value,d=k(c,{cursorStart:b.selectionStart,cursorEnd:b.selectionEnd}),e=d.cursorStart,f=d.cursorEnd,g=e!==f?"range":"single",h=null,i=j(x,"&#65279;").attr("data-mce-type","bookmark");if("range"===g){var l=b.value.slice(e,f),m=i.clone().addClass("mce_SELRES_end");h=[l,m[0].outerHTML].join("")}b.value=[b.value.slice(0,e),i.clone().addClass("mce_SELRES_start")[0].outerHTML,h,b.value.slice(f)].join("")}}function m(a){var b=a.$(".mce_SELRES_start").attr("data-mce-bogus",1),c=a.$(".mce_SELRES_end").attr("data-mce-bogus",1);if(b.length)if(a.focus(),c.length){var d=a.getDoc().createRange();d.setStartAfter(b[0]),d.setEndBefore(c[0]),a.selection.setRng(d)}else a.selection.select(b[0]);a.getParam("wp_keep_scroll_position")&&o(a,b),n(b),n(c),a.save()}function n(a){var b=a.parent();a.remove(),!b.is("p")||b.children().length||b.text()||b.remove()}function o(b,c){var e,f=b.$(c).offset().top,g=b.$(b.getContentAreaContainer()).offset().top,h=d(b),i=a("#wp-content-editor-tools"),j=0,k=0;i.length&&(j=i.height(),k=i.offset().top);var l=window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight,m=g+f,n=l-(j+h);if(!(m<n)){var o;b.settings.wp_autoresize_on?(e=a("html,body"),o=Math.max(m-n/2,k-j)):(e=a(b.contentDocument).find("html,body"),o=f),e.animate({scrollTop:parseInt(o,10)},100)}}function p(a){a.content=a.content.replace(/<p>(?:<br ?\/?>|\u00a0|\uFEFF| )*<\/p>/g,"<p>&nbsp;</p>")}function q(a){var b=a.getWin(),c=b.getSelection();if(c&&!(c.rangeCount<1)){var d="SELRES_"+Math.random(),e=j(a.$,d),f=e.clone().addClass("mce_SELRES_start"),g=e.clone().addClass("mce_SELRES_end"),h=c.getRangeAt(0),i=h.startContainer,k=h.startOffset,l=h.cloneRange();a.$(i).parents(".mce-offscreen-selection").length>0?(i=a.$("[data-mce-selected]")[0],f.attr("data-mce-object-selection","true"),g.attr("data-mce-object-selection","true"),a.$(i).before(f[0]),a.$(i).after(g[0])):(l.collapse(!1),l.insertNode(g[0]),l.setStart(i,k),l.collapse(!0),l.insertNode(f[0]),h.setStartAfter(f[0]),h.setEndBefore(g[0]),c.removeAllRanges(),c.addRange(h)),a.on("GetContent",p);var m=s(a.getContent());a.off("GetContent",p),f.remove(),g.remove();var n=new RegExp('<span[^>]*\\s*class="mce_SELRES_start"[^>]+>\\s*'+d+"[^<]*<\\/span>(\\s*)"),o=new RegExp('(\\s*)<span[^>]*\\s*class="mce_SELRES_end"[^>]+>\\s*'+d+"[^<]*<\\/span>"),q=m.match(n),r=m.match(o);if(!q)return null;var t=q.index,u=q[0].length,v=null;if(r){q[0].indexOf("data-mce-object-selection")!==-1&&(u-=q[1].length);var w=r.index;r[0].indexOf("data-mce-object-selection")!==-1&&(w-=r[1].length),v=w-u}return{start:t,end:v}}}function r(a,b){if(b){var c=a.getElement(),d=b.start,e=b.end||b.start;c.focus&&setTimeout(function(){c.setSelectionRange(d,e),c.blur&&c.blur(),c.focus()},100)}}function s(a){var b="blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset|figure",c=b+"|div|p",d=b+"|pre",e=!1,f=!1,g=[];return a?(a.indexOf("<script")===-1&&a.indexOf("<style")===-1||(a=a.replace(/<(script|style)[^>]*>[\s\S]*?<\/\1>/g,function(a){return g.push(a),"<wp-preserve>"})),a.indexOf("<pre")!==-1&&(e=!0,a=a.replace(/<pre[^>]*>[\s\S]+?<\/pre>/g,function(a){return a=a.replace(/<br ?\/?>(\r\n|\n)?/g,"<wp-line-break>"),a=a.replace(/<\/?p( [^>]*)?>(\r\n|\n)?/g,"<wp-line-break>"),a.replace(/\r?\n/g,"<wp-line-break>")})),a.indexOf("[caption")!==-1&&(f=!0,a=a.replace(/\[caption[\s\S]+?\[\/caption\]/g,function(a){return a.replace(/<br([^>]*)>/g,"<wp-temp-br$1>").replace(/[\r\n\t]+/,"")})),a=a.replace(new RegExp("\\s*</("+c+")>\\s*","g"),"</$1>\n"),a=a.replace(new RegExp("\\s*<((?:"+c+")(?: [^>]*)?)>","g"),"\n<$1>"),a=a.replace(/(<p [^>]+>.*?)<\/p>/g,"$1</p#>"),a=a.replace(/<div( [^>]*)?>\s*<p>/gi,"<div$1>\n\n"),a=a.replace(/\s*<p>/gi,""),a=a.replace(/\s*<\/p>\s*/gi,"\n\n"),a=a.replace(/\n[\s\u00a0]+\n/g,"\n\n"),a=a.replace(/(\s*)<br ?\/?>\s*/gi,function(a,b){return b&&b.indexOf("\n")!==-1?"\n\n":"\n"}),a=a.replace(/\s*<div/g,"\n<div"),a=a.replace(/<\/div>\s*/g,"</div>\n"),a=a.replace(/\s*\[caption([^\[]+)\[\/caption\]\s*/gi,"\n\n[caption$1[/caption]\n\n"),a=a.replace(/caption\]\n\n+\[caption/g,"caption]\n\n[caption"),a=a.replace(new RegExp("\\s*<((?:"+d+")(?: [^>]*)?)\\s*>","g"),"\n<$1>"),a=a.replace(new RegExp("\\s*</("+d+")>\\s*","g"),"</$1>\n"),a=a.replace(/<((li|dt|dd)[^>]*)>/g," \t<$1>"),a.indexOf("<option")!==-1&&(a=a.replace(/\s*<option/g,"\n<option"),a=a.replace(/\s*<\/select>/g,"\n</select>")),a.indexOf("<hr")!==-1&&(a=a.replace(/\s*<hr( [^>]*)?>\s*/g,"\n\n<hr$1>\n\n")),a.indexOf("<object")!==-1&&(a=a.replace(/<object[\s\S]+?<\/object>/g,function(a){return a.replace(/[\r\n]+/g,"")})),a=a.replace(/<\/p#>/g,"</p>\n"),a=a.replace(/\s*(<p [^>]+>[\s\S]*?<\/p>)/g,"\n$1"),a=a.replace(/^\s+/,""),a=a.replace(/[\s\u00a0]+$/,""),e&&(a=a.replace(/<wp-line-break>/g,"\n")),f&&(a=a.replace(/<wp-temp-br([^>]*)>/g,"<br$1>")),g.length&&(a=a.replace(/<wp-preserve>/g,function(){return g.shift()})),a):""}function t(a){var b=!1,c=!1,d="table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary";return a=a.replace(/\r\n|\r/g,"\n"),a.indexOf("<object")!==-1&&(a=a.replace(/<object[\s\S]+?<\/object>/g,function(a){return a.replace(/\n+/g,"")})),a=a.replace(/<[^<>]+>/g,function(a){return a.replace(/[\n\t ]+/g," ")}),a.indexOf("<pre")===-1&&a.indexOf("<script")===-1||(b=!0,a=a.replace(/<(pre|script)[^>]*>[\s\S]*?<\/\1>/g,function(a){return a.replace(/\n/g,"<wp-line-break>")})),a.indexOf("<figcaption")!==-1&&(a=a.replace(/\s*(<figcaption[^>]*>)/g,"$1"),a=a.replace(/<\/figcaption>\s*/g,"</figcaption>")),a.indexOf("[caption")!==-1&&(c=!0,a=a.replace(/\[caption[\s\S]+?\[\/caption\]/g,function(a){return a=a.replace(/<br([^>]*)>/g,"<wp-temp-br$1>"),a=a.replace(/<[^<>]+>/g,function(a){return a.replace(/[\n\t ]+/," ")}),a.replace(/\s*\n\s*/g,"<wp-temp-br />")})),a+="\n\n",a=a.replace(/<br \/>\s*<br \/>/gi,"\n\n"),a=a.replace(new RegExp("(<(?:"+d+")(?: [^>]*)?>)","gi"),"\n\n$1"),a=a.replace(new RegExp("(</(?:"+d+")>)","gi"),"$1\n\n"),a=a.replace(/<hr( [^>]*)?>/gi,"<hr$1>\n\n"),a=a.replace(/\s*<option/gi,"<option"),a=a.replace(/<\/option>\s*/gi,"</option>"),a=a.replace(/\n\s*\n+/g,"\n\n"),a=a.replace(/([\s\S]+?)\n\n/g,"<p>$1</p>\n"),a=a.replace(/<p>\s*?<\/p>/gi,""),a=a.replace(new RegExp("<p>\\s*(</?(?:"+d+")(?: [^>]*)?>)\\s*</p>","gi"),"$1"),a=a.replace(/<p>(<li.+?)<\/p>/gi,"$1"),a=a.replace(/<p>\s*<blockquote([^>]*)>/gi,"<blockquote$1><p>"),a=a.replace(/<\/blockquote>\s*<\/p>/gi,"</p></blockquote>"),a=a.replace(new RegExp("<p>\\s*(</?(?:"+d+")(?: [^>]*)?>)","gi"),"$1"),a=a.replace(new RegExp("(</?(?:"+d+")(?: [^>]*)?>)\\s*</p>","gi"),"$1"),a=a.replace(/(<br[^>]*>)\s*\n/gi,"$1"),a=a.replace(/\s*\n/g,"<br />\n"),a=a.replace(new RegExp("(</?(?:"+d+")[^>]*>)\\s*<br />","gi"),"$1"),a=a.replace(/<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi,"$1"),a=a.replace(/(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi,"[caption$1[/caption]"),a=a.replace(/(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g,function(a,b,c){return c.match(/<p( [^>]*)?>/)?a:b+"<p>"+c+"</p>"}),b&&(a=a.replace(/<wp-line-break>/g,"\n")),c&&(a=a.replace(/<wp-temp-br([^>]*)>/g,"<br$1>")),a}function u(b){var c={o:y,data:b,unfiltered:b};return a&&a("body").trigger("beforePreWpautop",[c]),c.data=s(c.data),a&&a("body").trigger("afterPreWpautop",[c]),c.data}function v(b){var c={o:y,data:b,unfiltered:b};return a&&a("body").trigger("beforeWpautop",[c]),c.data=t(c.data),a&&a("body").trigger("afterWpautop",[c]),c.data}var w,x,y={};return a(document).on("tinymce-editor-init.keep-scroll-position",function(a,b){b.$(".mce_SELRES_start").length&&m(b)}),a?a(document).ready(c):document.addEventListener?(document.addEventListener("DOMContentLoaded",c,!1),window.addEventListener("load",c,!1)):window.attachEvent&&(window.attachEvent("onload",c),document.attachEvent("onreadystatechange",function(){"complete"===document.readyState&&c()})),b.editor.autop=v,b.editor.removep=u,y={go:e,wpautop:v,pre_wpautop:u,_wp_Autop:t,_wp_Nop:s}}b.editor=b.editor||{},window.switchEditors=new c,b.editor.initialize=function(c,d){var e,f;if(a&&c&&b.editor.getDefaultSettings){if(f=b.editor.getDefaultSettings(),d||(d={tinymce:!0}),d.tinymce&&d.quicktags){var g=a("#"+c),h=a("<div>").attr({"class":"wp-core-ui wp-editor-wrap tmce-active",id:"wp-"+c+"-wrap"}),i=a('<div class="wp-editor-container">'),j=a("<button>").attr({type:"button","data-wp-editor-id":c}),k=a('<div class="wp-editor-tools">');if(d.mediaButtons){var l="Add Media";window._wpMediaViewsL10n&&window._wpMediaViewsL10n.addMedia&&(l=window._wpMediaViewsL10n.addMedia);var m=a('<button type="button" class="button insert-media add_media">');m.append('<span class="wp-media-buttons-icon"></span>'),m.append(document.createTextNode(" "+l)),m.data("editor",c),k.append(a('<div class="wp-media-buttons">').append(m))}h.append(k.append(a('<div class="wp-editor-tabs">').append(j.clone().attr({id:c+"-tmce","class":"wp-switch-editor switch-tmce"}).text(window.tinymce.translate("Visual"))).append(j.attr({id:c+"-html","class":"wp-switch-editor switch-html"}).text(window.tinymce.translate("Text")))).append(i)),g.after(h),i.append(g)}window.tinymce&&d.tinymce&&("object"!=typeof d.tinymce&&(d.tinymce={}),e=a.extend({},f.tinymce,d.tinymce),e.selector="#"+c,a(document).trigger("wp-before-tinymce-init",e),window.tinymce.init(e),window.wpActiveEditor||(window.wpActiveEditor=c)),window.quicktags&&d.quicktags&&("object"!=typeof d.quicktags&&(d.quicktags={}),e=a.extend({},f.quicktags,d.quicktags),e.id=c,a(document).trigger("wp-before-quicktags-init",e),window.quicktags(e),window.wpActiveEditor||(window.wpActiveEditor=e.id))}},b.editor.remove=function(b){var c,d,e=a("#wp-"+b+"-wrap");window.tinymce&&(c=window.tinymce.get(b),c&&(c.isHidden()||c.save(),c.remove())),window.quicktags&&(d=window.QTags.getInstance(b),d&&d.remove()),e.length&&(e.after(a("#"+b)),e.remove())},b.editor.getContent=function(b){var c;if(a&&b)return window.tinymce&&(c=window.tinymce.get(b),c&&!c.isHidden()&&c.save()),a("#"+b).val()}}(window.jQuery,window.wp);
function quicktags(a){return new QTags(a)}function edInsertContent(a,b){return QTags.insertContent(b)}function edButton(a,b,c,d,e){return QTags.addButton(a,b,c,d,e,"",-1)}var QTags,edCanvas,edButtons=[],edAddTag=function(){},edCheckOpenTags=function(){},edCloseAllTags=function(){},edInsertImage=function(){},edInsertLink=function(){},edInsertTag=function(){},edLink=function(){},edQuickLink=function(){},edRemoveTag=function(){},edShowButton=function(){},edShowLinks=function(){},edSpell=function(){},edToolbar=function(){};!function(){function a(a){return a=a||"",a=a.replace(/&([^#])(?![a-z1-4]{1,8};)/gi,"&#038;$1"),a.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;")}var b,c=function(a){var b,d,e,f;"undefined"!=typeof jQuery?jQuery(document).ready(a):(b=c,b.funcs=[],b.ready=function(){if(!b.isReady)for(b.isReady=!0,d=0;d<b.funcs.length;d++)b.funcs[d]()},b.isReady?a():b.funcs.push(a),b.eventAttached||(document.addEventListener?(e=function(){document.removeEventListener("DOMContentLoaded",e,!1),b.ready()},document.addEventListener("DOMContentLoaded",e,!1),window.addEventListener("load",b.ready,!1)):document.attachEvent&&(e=function(){"complete"===document.readyState&&(document.detachEvent("onreadystatechange",e),b.ready())},document.attachEvent("onreadystatechange",e),window.attachEvent("onload",b.ready),(f=function(){try{document.documentElement.doScroll("left")}catch(a){return void setTimeout(f,50)}b.ready()})()),b.eventAttached=!0))},d=function(){var a,b=new Date;return a=function(a){var b=a.toString();return b.length<2&&(b="0"+b),b},b.getUTCFullYear()+"-"+a(b.getUTCMonth()+1)+"-"+a(b.getUTCDate())+"T"+a(b.getUTCHours())+":"+a(b.getUTCMinutes())+":"+a(b.getUTCSeconds())+"+00:00"}();b=QTags=function(a){if("string"==typeof a)a={id:a};else if("object"!=typeof a)return!1;var d,e,f,g,h,i=this,j=a.id,k=document.getElementById(j),l="qt_"+j;return!(!j||!k)&&(i.name=l,i.id=j,i.canvas=k,i.settings=a,"content"!==j||"string"!=typeof adminpage||"post-new-php"!==adminpage&&"post-php"!==adminpage?f=l+"_toolbar":(edCanvas=k,f="ed_toolbar"),d=document.getElementById(f),d||(d=document.createElement("div"),d.id=f,d.className="quicktags-toolbar"),k.parentNode.insertBefore(d,k),i.toolbar=d,e=function(a){a=a||window.event;var b,c=a.target||a.srcElement,d=c.clientWidth||c.offsetWidth;d&&/ ed_button /.test(" "+c.className+" ")&&(i.canvas=k=document.getElementById(j),b=c.id.replace(l+"_",""),i.theButtons[b]&&i.theButtons[b].callback.call(i.theButtons[b],c,k,i))},h=function(){window.wpActiveEditor=j},g=document.getElementById("wp-"+j+"-wrap"),d.addEventListener?(d.addEventListener("click",e,!1),g&&g.addEventListener("click",h,!1)):d.attachEvent&&(d.attachEvent("onclick",e),g&&g.attachEvent("onclick",h)),i.getButton=function(a){return i.theButtons[a]},i.getButtonElement=function(a){return document.getElementById(l+"_"+a)},i.init=function(){c(function(){b._buttonsInit(j)})},i.remove=function(){delete b.instances[j],d&&d.parentNode&&d.parentNode.removeChild(d)},b.instances[j]=i,void i.init())},b.instances={},b.getInstance=function(a){return b.instances[a]},b._buttonsInit=function(a){function c(a){var c,e,f,g,h,i,j,k,l,m=",strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,";i=d.instances[a],c=i.canvas,e=i.name,f=i.settings,h="",g={},l="",f.buttons&&(l=","+f.buttons+",");for(k in edButtons)edButtons[k]&&(j=edButtons[k].id,l&&m.indexOf(","+j+",")!==-1&&l.indexOf(","+j+",")===-1||edButtons[k].instance&&edButtons[k].instance!==a||(g[j]=edButtons[k],edButtons[k].html&&(h+=edButtons[k].html(e+"_"))));l&&l.indexOf(",dfw,")!==-1&&(g.dfw=new b.DFWButton,h+=g.dfw.html(e+"_")),"rtl"===document.getElementsByTagName("html")[0].dir&&(g.textdirection=new b.TextDirectionButton,h+=g.textdirection.html(e+"_")),i.toolbar.innerHTML=h,i.theButtons=g,"undefined"!=typeof jQuery&&jQuery(document).triggerHandler("quicktags-init",[i])}var d=this;if(a)c(a);else for(a in d.instances)c(a);d.buttonsInitDone=!0},b.addButton=function(a,c,d,e,f,g,h,i,j){var k;if(a&&c){if(h=h||0,e=e||"",j=j||{},"function"==typeof d)k=new b.Button(a,c,f,g,i,j),k.callback=d;else{if("string"!=typeof d)return;k=new b.TagButton(a,c,d,e,f,g,i,j)}if(h===-1)return k;if(h>0){for(;"undefined"!=typeof edButtons[h];)h++;edButtons[h]=k}else edButtons[edButtons.length]=k;this.buttonsInitDone&&this._buttonsInit()}},b.insertContent=function(a){var b,c,d,e,f,g,h=document.getElementById(wpActiveEditor);return!!h&&(document.selection?(h.focus(),b=document.selection.createRange(),b.text=a,h.focus()):h.selectionStart||0===h.selectionStart?(f=h.value,c=h.selectionStart,d=h.selectionEnd,e=h.scrollTop,h.value=f.substring(0,c)+a+f.substring(d,f.length),h.selectionStart=c+a.length,h.selectionEnd=c+a.length,h.scrollTop=e,h.focus()):(h.value+=a,h.focus()),document.createEvent?(g=document.createEvent("HTMLEvents"),g.initEvent("change",!1,!0),h.dispatchEvent(g)):h.fireEvent&&h.fireEvent("onchange"),!0)},b.Button=function(a,b,c,d,e,f){this.id=a,this.display=b,this.access="",this.title=d||"",this.instance=e||"",this.attr=f||{}},b.Button.prototype.html=function(b){var c,d,e,f=this.title?' title="'+a(this.title)+'"':"",g=this.attr&&this.attr.ariaLabel?' aria-label="'+a(this.attr.ariaLabel)+'"':"",h=this.display?' value="'+a(this.display)+'"':"",i=this.id?' id="'+a(b+this.id)+'"':"",j=(e=window.wp)&&e.editor&&e.editor.dfw;return"fullscreen"===this.id?'<button type="button"'+i+' class="ed_button qt-dfw qt-fullscreen"'+f+g+"></button>":"dfw"===this.id?(c=j&&j.isActive()?"":' disabled="disabled"',d=j&&j.isOn()?" active":"",'<button type="button"'+i+' class="ed_button qt-dfw'+d+'"'+f+g+c+"></button>"):'<input type="button"'+i+' class="ed_button button button-small"'+f+g+h+" />"},b.Button.prototype.callback=function(){},b.TagButton=function(a,c,d,e,f,g,h,i){var j=this;b.Button.call(j,a,c,f,g,h,i),j.tagStart=d,j.tagEnd=e},b.TagButton.prototype=new b.Button,b.TagButton.prototype.openTag=function(a,b){b.openTags||(b.openTags=[]),this.tagEnd&&(b.openTags.push(this.id),a.value="/"+a.value,this.attr.ariaLabelClose&&a.setAttribute("aria-label",this.attr.ariaLabelClose))},b.TagButton.prototype.closeTag=function(a,b){var c=this.isOpen(b);c!==!1&&b.openTags.splice(c,1),a.value=this.display,this.attr.ariaLabel&&a.setAttribute("aria-label",this.attr.ariaLabel)},b.TagButton.prototype.isOpen=function(a){var b=this,c=0,d=!1;if(a.openTags)for(;d===!1&&c<a.openTags.length;)d=a.openTags[c]===b.id&&c,c++;else d=!1;return d},b.TagButton.prototype.callback=function(a,b,c){var d,e,f,g,h,i,j,k,l,m=this,n=b.value,o=n?m.tagEnd:"";document.selection?(b.focus(),k=document.selection.createRange(),k.text.length>0?m.tagEnd?k.text=m.tagStart+k.text+o:k.text=k.text+m.tagStart:m.tagEnd?m.isOpen(c)===!1?(k.text=m.tagStart,m.openTag(a,c)):(k.text=o,m.closeTag(a,c)):k.text=m.tagStart,b.focus()):b.selectionStart||0===b.selectionStart?(d=b.selectionStart,e=b.selectionEnd,d<e&&"\n"===n.charAt(e-1)&&(e-=1),f=e,g=b.scrollTop,h=n.substring(0,d),i=n.substring(e,n.length),j=n.substring(d,e),d!==e?m.tagEnd?(b.value=h+m.tagStart+j+o+i,f+=m.tagStart.length+o.length):(b.value=h+j+m.tagStart+i,f+=m.tagStart.length):m.tagEnd?m.isOpen(c)===!1?(b.value=h+m.tagStart+i,m.openTag(a,c),f=d+m.tagStart.length):(b.value=h+o+i,f=d+o.length,m.closeTag(a,c)):(b.value=h+m.tagStart+i,f=d+m.tagStart.length),b.selectionStart=f,b.selectionEnd=f,b.scrollTop=g,b.focus()):(o?m.isOpen(c)!==!1?(b.value+=m.tagStart,m.openTag(a,c)):(b.value+=o,m.closeTag(a,c)):b.value+=m.tagStart,b.focus()),document.createEvent?(l=document.createEvent("HTMLEvents"),l.initEvent("change",!1,!0),b.dispatchEvent(l)):b.fireEvent&&b.fireEvent("onchange")},b.SpellButton=function(){},b.CloseButton=function(){b.Button.call(this,"close",quicktagsL10n.closeTags,"",quicktagsL10n.closeAllOpenTags)},b.CloseButton.prototype=new b.Button,b._close=function(a,b,c){var d,e,f=c.openTags;if(f)for(;f.length>0;)d=c.getButton(f[f.length-1]),e=document.getElementById(c.name+"_"+d.id),a?d.callback.call(d,e,b,c):d.closeTag(e,c)},b.CloseButton.prototype.callback=b._close,b.closeAllTags=function(a){var c=this.getInstance(a);c&&b._close("",c.canvas,c)},b.LinkButton=function(){var a={ariaLabel:quicktagsL10n.link};b.TagButton.call(this,"link","link","","</a>","","","",a)},b.LinkButton.prototype=new b.TagButton,b.LinkButton.prototype.callback=function(a,c,d,e){var f,g=this;return"undefined"!=typeof wpLink?void wpLink.open(d.id):(e||(e="http://"),void(g.isOpen(d)===!1?(f=prompt(quicktagsL10n.enterURL,e),f&&(g.tagStart='<a href="'+f+'">',b.TagButton.prototype.callback.call(g,a,c,d))):b.TagButton.prototype.callback.call(g,a,c,d)))},b.ImgButton=function(){var a={ariaLabel:quicktagsL10n.image};b.TagButton.call(this,"img","img","","","","","",a)},b.ImgButton.prototype=new b.TagButton,b.ImgButton.prototype.callback=function(a,c,d,e){e||(e="http://");var f,g=prompt(quicktagsL10n.enterImageURL,e);g&&(f=prompt(quicktagsL10n.enterImageDescription,""),this.tagStart='<img src="'+g+'" alt="'+f+'" />',b.TagButton.prototype.callback.call(this,a,c,d))},b.DFWButton=function(){b.Button.call(this,"dfw","","f",quicktagsL10n.dfw)},b.DFWButton.prototype=new b.Button,b.DFWButton.prototype.callback=function(){var a;(a=window.wp)&&a.editor&&a.editor.dfw&&window.wp.editor.dfw.toggle()},b.TextDirectionButton=function(){b.Button.call(this,"textdirection",quicktagsL10n.textdirection,"",quicktagsL10n.toggleTextdirection)},b.TextDirectionButton.prototype=new b.Button,b.TextDirectionButton.prototype.callback=function(a,b){var c="rtl"===document.getElementsByTagName("html")[0].dir,d=b.style.direction;d||(d=c?"rtl":"ltr"),b.style.direction="rtl"===d?"ltr":"rtl",b.focus()},edButtons[10]=new b.TagButton("strong","b","<strong>","</strong>","","","",{ariaLabel:quicktagsL10n.strong,ariaLabelClose:quicktagsL10n.strongClose}),edButtons[20]=new b.TagButton("em","i","<em>","</em>","","","",{ariaLabel:quicktagsL10n.em,ariaLabelClose:quicktagsL10n.emClose}),edButtons[30]=new b.LinkButton,edButtons[40]=new b.TagButton("block","b-quote","\n\n<blockquote>","</blockquote>\n\n","","","",{ariaLabel:quicktagsL10n.blockquote,ariaLabelClose:quicktagsL10n.blockquoteClose}),edButtons[50]=new b.TagButton("del","del",'<del datetime="'+d+'">',"</del>","","","",{ariaLabel:quicktagsL10n.del,ariaLabelClose:quicktagsL10n.delClose}),edButtons[60]=new b.TagButton("ins","ins",'<ins datetime="'+d+'">',"</ins>","","","",{ariaLabel:quicktagsL10n.ins,ariaLabelClose:quicktagsL10n.insClose}),edButtons[70]=new b.ImgButton,edButtons[80]=new b.TagButton("ul","ul","<ul>\n","</ul>\n\n","","","",{ariaLabel:quicktagsL10n.ul,ariaLabelClose:quicktagsL10n.ulClose}),edButtons[90]=new b.TagButton("ol","ol","<ol>\n","</ol>\n\n","","","",{ariaLabel:quicktagsL10n.ol,ariaLabelClose:quicktagsL10n.olClose}),edButtons[100]=new b.TagButton("li","li","\t<li>","</li>\n","","","",{ariaLabel:quicktagsL10n.li,ariaLabelClose:quicktagsL10n.liClose}),edButtons[110]=new b.TagButton("code","code","<code>","</code>","","","",{ariaLabel:quicktagsL10n.code,ariaLabelClose:quicktagsL10n.codeClose}),edButtons[120]=new b.TagButton("more","more","<!--more-->\n\n","","","","",{ariaLabel:quicktagsL10n.more}),edButtons[140]=new b.CloseButton}();
window.wp=window.wp||{},function(a,b){"use strict";function c(a,c){e(),a=b("<p>").html(a).text(),h===a&&(a+="\xa0"),h=a,g&&"assertive"===c?g.text(a):f&&f.text(a)}function d(a){a=a||"polite";var c=b("<div>",{id:"wp-a11y-speak-"+a,"aria-live":a,"aria-relevant":"additions text","aria-atomic":"true","class":"screen-reader-text wp-a11y-speak-region"});return b(document.body).append(c),c}function e(){b(".wp-a11y-speak-region").text("")}var f,g,h="";b(document).ready(function(){f=b("#wp-a11y-speak-polite"),g=b("#wp-a11y-speak-assertive"),f.length||(f=d("polite")),g.length||(g=d("assertive"))}),a.a11y=a.a11y||{},a.a11y.speak=c}(window.wp,window.jQuery);
var wpLink;!function(a,b,c){function d(){return j||e.dom.getParent(e.selection.getNode(),"a[href]")}var e,f,g,h,i,j,k=/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,63}$/i,l=/^(https?|ftp):\/\/[A-Z0-9.-]+\.[A-Z]{2,63}[^ "]*$/i,m={},n={},o="ontouchend"in document;wpLink={timeToTriggerRiver:150,minRiverAJAXDuration:200,riverBottomThreshold:5,keySensitivity:100,lastSearch:"",textarea:"",modalOpen:!1,init:function(){m.wrap=a("#wp-link-wrap"),m.dialog=a("#wp-link"),m.backdrop=a("#wp-link-backdrop"),m.submit=a("#wp-link-submit"),m.close=a("#wp-link-close"),m.text=a("#wp-link-text"),m.url=a("#wp-link-url"),m.nonce=a("#_ajax_linking_nonce"),m.openInNewTab=a("#wp-link-target"),m.search=a("#wp-link-search"),n.search=new g(a("#search-results")),n.recent=new g(a("#most-recent-results")),n.elements=m.dialog.find(".query-results"),m.queryNotice=a("#query-notice-message"),m.queryNoticeTextDefault=m.queryNotice.find(".query-notice-default"),m.queryNoticeTextHint=m.queryNotice.find(".query-notice-hint"),m.dialog.keydown(wpLink.keydown),m.dialog.keyup(wpLink.keyup),m.submit.click(function(a){a.preventDefault(),wpLink.update()}),m.close.add(m.backdrop).add("#wp-link-cancel button").click(function(a){a.preventDefault(),wpLink.close()}),n.elements.on("river-select",wpLink.updateFields),m.search.on("focus.wplink",function(){m.queryNoticeTextDefault.hide(),m.queryNoticeTextHint.removeClass("screen-reader-text").show()}).on("blur.wplink",function(){m.queryNoticeTextDefault.show(),m.queryNoticeTextHint.addClass("screen-reader-text").hide()}),m.search.on("keyup input",function(){window.clearTimeout(f),f=window.setTimeout(function(){wpLink.searchInternalLinks()},500)}),m.url.on("paste",function(){setTimeout(wpLink.correctURL,0)}),m.url.on("blur",wpLink.correctURL)},correctURL:function(){var b=a.trim(m.url.val());b&&i!==b&&!/^(?:[a-z]+:|#|\?|\.|\/)/.test(b)&&(m.url.val("http://"+b),i=b)},open:function(b,c,d,f){var g,h=a(document.body);h.addClass("modal-open"),wpLink.modalOpen=!0,j=f,wpLink.range=null,b&&(window.wpActiveEditor=b),window.wpActiveEditor&&(this.textarea=a("#"+window.wpActiveEditor).get(0),"undefined"!=typeof window.tinymce&&(h.append(m.backdrop,m.wrap),g=window.tinymce.get(window.wpActiveEditor),e=g&&!g.isHidden()?g:null),!wpLink.isMCE()&&document.selection&&(this.textarea.focus(),this.range=document.selection.createRange()),m.wrap.show(),m.backdrop.show(),wpLink.refresh(c,d),a(document).trigger("wplink-open",m.wrap))},isMCE:function(){return e&&!e.isHidden()},refresh:function(a,b){var c="";n.search.refresh(),n.recent.refresh(),wpLink.isMCE()?wpLink.mceRefresh(a,b):(m.wrap.hasClass("has-text-field")||m.wrap.addClass("has-text-field"),document.selection?c=document.selection.createRange().text||b||"":"undefined"!=typeof this.textarea.selectionStart&&this.textarea.selectionStart!==this.textarea.selectionEnd&&(b=this.textarea.value.substring(this.textarea.selectionStart,this.textarea.selectionEnd)||b||""),m.text.val(b),wpLink.setDefaultValues()),o?m.url.focus().blur():window.setTimeout(function(){m.url[0].select(),m.url.focus()}),n.recent.ul.children().length||n.recent.ajax(),i=m.url.val().replace(/^http:\/\//,"")},hasSelectedText:function(a){var b,c,d,f=e.selection.getContent();if(/</.test(f)&&(!/^<a [^>]+>[^<]+<\/a>$/.test(f)||f.indexOf("href=")===-1))return!1;if(a){if(c=a.childNodes,0===c.length)return!1;for(d=c.length-1;d>=0;d--)if(b=c[d],3!=b.nodeType&&!window.tinymce.dom.BookmarkManager.isBookmarkNode(b))return!1}return!0},mceRefresh:function(c,f){var g,h,i=d(),j=this.hasSelectedText(i);i?(g=i.textContent||i.innerText,h=e.dom.getAttrib(i,"href"),a.trim(g)||(g=f||""),c&&(l.test(c)||k.test(c))&&(h=c),"_wp_link_placeholder"!==h?(m.url.val(h),m.openInNewTab.prop("checked","_blank"===e.dom.getAttrib(i,"target")),m.submit.val(b.update)):this.setDefaultValues(g),c&&c!==h?m.search.val(c):m.search.val(""),window.setTimeout(function(){wpLink.searchInternalLinks()})):(g=e.selection.getContent({format:"text"})||f||"",this.setDefaultValues(g)),j?(m.text.val(g),m.wrap.addClass("has-text-field")):(m.text.val(""),m.wrap.removeClass("has-text-field"))},close:function(b){a(document.body).removeClass("modal-open"),wpLink.modalOpen=!1,"noReset"!==b&&(wpLink.isMCE()?(e.plugins.wplink&&e.plugins.wplink.close(),e.focus()):(wpLink.textarea.focus(),wpLink.range&&(wpLink.range.moveToBookmark(wpLink.range.getBookmark()),wpLink.range.select()))),m.backdrop.hide(),m.wrap.hide(),i=!1,a(document).trigger("wplink-close",m.wrap)},getAttrs:function(){return wpLink.correctURL(),{href:a.trim(m.url.val()),target:m.openInNewTab.prop("checked")?"_blank":null}},buildHtml:function(a){var b='<a href="'+a.href+'"';return a.target&&(b+=' rel="noopener" target="'+a.target+'"'),b+">"},update:function(){wpLink.isMCE()?wpLink.mceUpdate():wpLink.htmlUpdate()},htmlUpdate:function(){var d,e,f,g,h,i,j,k=wpLink.textarea;if(k){d=wpLink.getAttrs(),e=m.text.val();var l=document.createElement("a");l.href=d.href,"javascript:"!==l.protocol&&"data:"!==l.protocol||(d.href=""),d.href&&(f=wpLink.buildHtml(d),document.selection&&wpLink.range?(k.focus(),wpLink.range.text=f+(e||wpLink.range.text)+"</a>",wpLink.range.moveToBookmark(wpLink.range.getBookmark()),wpLink.range.select(),wpLink.range=null):"undefined"!=typeof k.selectionStart&&(g=k.selectionStart,h=k.selectionEnd,j=e||k.value.substring(g,h),f=f+j+"</a>",i=g+f.length,g!==h||j||(i-=4),k.value=k.value.substring(0,g)+f+k.value.substring(h,k.value.length),k.selectionStart=k.selectionEnd=i),wpLink.close(),k.focus(),a(k).trigger("change"),c.a11y.speak(b.linkInserted))}},mceUpdate:function(){var f,g,h,i,j=wpLink.getAttrs(),k=document.createElement("a");return k.href=j.href,"javascript:"!==k.protocol&&"data:"!==k.protocol||(j.href=""),j.href?(f=e.$(d()),e.undoManager.transact(function(){f.length||(e.execCommand("mceInsertLink",!1,{href:"_wp_link_placeholder","data-wp-temp-link":1}),f=e.$('a[data-wp-temp-link="1"]').removeAttr("data-wp-temp-link"),h=a.trim(f.text())),f.length?(m.wrap.hasClass("has-text-field")&&(g=m.text.val(),g?f.text(g):h||f.text(j.href)),j["data-wplink-edit"]=null,j["data-mce-href"]=null,f.attr(j)):e.execCommand("unlink")}),wpLink.close("noReset"),e.focus(),f.length&&(i=f.parent("#_mce_caret"),i.length&&i.before(f.removeAttr("data-mce-bogus")),e.selection.select(f[0]),e.selection.collapse(),e.plugins.wplink&&e.plugins.wplink.checkLink(f[0])),e.nodeChanged(),void c.a11y.speak(b.linkInserted)):(e.execCommand("unlink"),void wpLink.close())},updateFields:function(a,b){m.url.val(b.children(".item-permalink").val())},getUrlFromSelection:function(b){return b||(this.isMCE()?b=e.selection.getContent({format:"text"}):document.selection&&wpLink.range?b=wpLink.range.text:"undefined"!=typeof this.textarea.selectionStart&&(b=this.textarea.value.substring(this.textarea.selectionStart,this.textarea.selectionEnd))),b=a.trim(b),b&&k.test(b)?"mailto:"+b:b&&l.test(b)?b.replace(/&amp;|&#0?38;/gi,"&"):""},setDefaultValues:function(a){m.url.val(this.getUrlFromSelection(a)),m.search.val(""),wpLink.searchInternalLinks(),m.submit.val(b.save)},searchInternalLinks:function(){var a,b=m.search.val()||"";if(b.length>2){if(n.recent.hide(),n.search.show(),wpLink.lastSearch==b)return;wpLink.lastSearch=b,a=m.search.parent().find(".spinner").addClass("is-active"),n.search.change(b),n.search.ajax(function(){a.removeClass("is-active")})}else n.search.hide(),n.recent.show()},next:function(){n.search.next(),n.recent.next()},prev:function(){n.search.prev(),n.recent.prev()},keydown:function(a){var b,c;27===a.keyCode?(wpLink.close(),a.stopImmediatePropagation()):9===a.keyCode&&(c=a.target.id,"wp-link-submit"!==c||a.shiftKey?"wp-link-close"===c&&a.shiftKey&&(m.submit.focus(),a.preventDefault()):(m.close.focus(),a.preventDefault())),38!==a.keyCode&&40!==a.keyCode||(!document.activeElement||"link-title-field"!==document.activeElement.id&&"url-field"!==document.activeElement.id)&&(b=38===a.keyCode?"prev":"next",clearInterval(wpLink.keyInterval),wpLink[b](),wpLink.keyInterval=setInterval(wpLink[b],wpLink.keySensitivity),a.preventDefault())},keyup:function(a){38!==a.keyCode&&40!==a.keyCode||(clearInterval(wpLink.keyInterval),a.preventDefault())},delayedCallback:function(a,b){var c,d,e,f;return b?(setTimeout(function(){return d?a.apply(f,e):void(c=!0)},b),function(){return c?a.apply(this,arguments):(e=arguments,f=this,void(d=!0))}):a}},g=function(b,c){var d=this;this.element=b,this.ul=b.children("ul"),this.contentHeight=b.children("#link-selector-height"),this.waiting=b.find(".river-waiting"),this.change(c),this.refresh(),a("#wp-link .query-results, #wp-link #link-selector").scroll(function(){d.maybeLoad()}),b.on("click","li",function(b){d.select(a(this),b)})},a.extend(g.prototype,{refresh:function(){this.deselect(),this.visible=this.element.is(":visible")},show:function(){this.visible||(this.deselect(),this.element.show(),this.visible=!0)},hide:function(){this.element.hide(),this.visible=!1},select:function(a,b){var c,d,e,f;a.hasClass("unselectable")||a==this.selected||(this.deselect(),this.selected=a.addClass("selected"),c=a.outerHeight(),d=this.element.height(),e=a.position().top,f=this.element.scrollTop(),e<0?this.element.scrollTop(f+e):e+c>d&&this.element.scrollTop(f+e-d+c),this.element.trigger("river-select",[a,b,this]))},deselect:function(){this.selected&&this.selected.removeClass("selected"),this.selected=!1},prev:function(){if(this.visible){var a;this.selected&&(a=this.selected.prev("li"),a.length&&this.select(a))}},next:function(){if(this.visible){var b=this.selected?this.selected.next("li"):a("li:not(.unselectable):first",this.element);b.length&&this.select(b)}},ajax:function(a){var b=this,c=1==this.query.page?0:wpLink.minRiverAJAXDuration,d=wpLink.delayedCallback(function(c,d){b.process(c,d),a&&a(c,d)},c);this.query.ajax(d)},change:function(a){this.query&&this._search==a||(this._search=a,this.query=new h(a),this.element.scrollTop(0))},process:function(c,d){var e="",f=!0,g="",h=1==d.page;c?a.each(c,function(){g=f?"alternate":"",g+=this.title?"":" no-title",e+=g?'<li class="'+g+'">':"<li>",e+='<input type="hidden" class="item-permalink" value="'+this.permalink+'" />',e+='<span class="item-title">',e+=this.title?this.title:b.noTitle,e+='</span><span class="item-info">'+this.info+"</span></li>",f=!f}):h&&(e+='<li class="unselectable no-matches-found"><span class="item-title"><em>'+b.noMatchesFound+"</em></span></li>"),this.ul[h?"html":"append"](e)},maybeLoad:function(){var a=this,b=this.element,c=b.scrollTop()+b.height();!this.query.ready()||c<this.contentHeight.height()-wpLink.riverBottomThreshold||setTimeout(function(){var c=b.scrollTop(),d=c+b.height();!a.query.ready()||d<a.contentHeight.height()-wpLink.riverBottomThreshold||(a.waiting.addClass("is-active"),b.scrollTop(c+a.waiting.outerHeight()),a.ajax(function(){a.waiting.removeClass("is-active")}))},wpLink.timeToTriggerRiver)}}),h=function(a){this.page=1,this.allLoaded=!1,this.querying=!1,this.search=a},a.extend(h.prototype,{ready:function(){return!(this.querying||this.allLoaded)},ajax:function(b){var c=this,d={action:"wp-link-ajax",page:this.page,_ajax_linking_nonce:m.nonce.val()};this.search&&(d.search=this.search),this.querying=!0,a.post(window.ajaxurl,d,function(a){c.page++,c.querying=!1,c.allLoaded=!a,b(a,d)},"json")}}),a(document).ready(wpLink.init)}(jQuery,window.wpLinkL10n,window.wp);
/*!
 * jQuery UI Position 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/position/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(jQuery)}(function(a){return function(){function b(a,b,c){return[parseFloat(a[0])*(n.test(a[0])?b/100:1),parseFloat(a[1])*(n.test(a[1])?c/100:1)]}function c(b,c){return parseInt(a.css(b,c),10)||0}function d(b){var c=b[0];return 9===c.nodeType?{width:b.width(),height:b.height(),offset:{top:0,left:0}}:a.isWindow(c)?{width:b.width(),height:b.height(),offset:{top:b.scrollTop(),left:b.scrollLeft()}}:c.preventDefault?{width:0,height:0,offset:{top:c.pageY,left:c.pageX}}:{width:b.outerWidth(),height:b.outerHeight(),offset:b.offset()}}a.ui=a.ui||{};var e,f,g=Math.max,h=Math.abs,i=Math.round,j=/left|center|right/,k=/top|center|bottom/,l=/[\+\-]\d+(\.[\d]+)?%?/,m=/^\w+/,n=/%$/,o=a.fn.position;a.position={scrollbarWidth:function(){if(void 0!==e)return e;var b,c,d=a("<div style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'><div style='height:100px;width:auto;'></div></div>"),f=d.children()[0];return a("body").append(d),b=f.offsetWidth,d.css("overflow","scroll"),c=f.offsetWidth,b===c&&(c=d[0].clientWidth),d.remove(),e=b-c},getScrollInfo:function(b){var c=b.isWindow||b.isDocument?"":b.element.css("overflow-x"),d=b.isWindow||b.isDocument?"":b.element.css("overflow-y"),e="scroll"===c||"auto"===c&&b.width<b.element[0].scrollWidth,f="scroll"===d||"auto"===d&&b.height<b.element[0].scrollHeight;return{width:f?a.position.scrollbarWidth():0,height:e?a.position.scrollbarWidth():0}},getWithinInfo:function(b){var c=a(b||window),d=a.isWindow(c[0]),e=!!c[0]&&9===c[0].nodeType;return{element:c,isWindow:d,isDocument:e,offset:c.offset()||{left:0,top:0},scrollLeft:c.scrollLeft(),scrollTop:c.scrollTop(),width:d||e?c.width():c.outerWidth(),height:d||e?c.height():c.outerHeight()}}},a.fn.position=function(e){if(!e||!e.of)return o.apply(this,arguments);e=a.extend({},e);var n,p,q,r,s,t,u=a(e.of),v=a.position.getWithinInfo(e.within),w=a.position.getScrollInfo(v),x=(e.collision||"flip").split(" "),y={};return t=d(u),u[0].preventDefault&&(e.at="left top"),p=t.width,q=t.height,r=t.offset,s=a.extend({},r),a.each(["my","at"],function(){var a,b,c=(e[this]||"").split(" ");1===c.length&&(c=j.test(c[0])?c.concat(["center"]):k.test(c[0])?["center"].concat(c):["center","center"]),c[0]=j.test(c[0])?c[0]:"center",c[1]=k.test(c[1])?c[1]:"center",a=l.exec(c[0]),b=l.exec(c[1]),y[this]=[a?a[0]:0,b?b[0]:0],e[this]=[m.exec(c[0])[0],m.exec(c[1])[0]]}),1===x.length&&(x[1]=x[0]),"right"===e.at[0]?s.left+=p:"center"===e.at[0]&&(s.left+=p/2),"bottom"===e.at[1]?s.top+=q:"center"===e.at[1]&&(s.top+=q/2),n=b(y.at,p,q),s.left+=n[0],s.top+=n[1],this.each(function(){var d,j,k=a(this),l=k.outerWidth(),m=k.outerHeight(),o=c(this,"marginLeft"),t=c(this,"marginTop"),z=l+o+c(this,"marginRight")+w.width,A=m+t+c(this,"marginBottom")+w.height,B=a.extend({},s),C=b(y.my,k.outerWidth(),k.outerHeight());"right"===e.my[0]?B.left-=l:"center"===e.my[0]&&(B.left-=l/2),"bottom"===e.my[1]?B.top-=m:"center"===e.my[1]&&(B.top-=m/2),B.left+=C[0],B.top+=C[1],f||(B.left=i(B.left),B.top=i(B.top)),d={marginLeft:o,marginTop:t},a.each(["left","top"],function(b,c){a.ui.position[x[b]]&&a.ui.position[x[b]][c](B,{targetWidth:p,targetHeight:q,elemWidth:l,elemHeight:m,collisionPosition:d,collisionWidth:z,collisionHeight:A,offset:[n[0]+C[0],n[1]+C[1]],my:e.my,at:e.at,within:v,elem:k})}),e.using&&(j=function(a){var b=r.left-B.left,c=b+p-l,d=r.top-B.top,f=d+q-m,i={target:{element:u,left:r.left,top:r.top,width:p,height:q},element:{element:k,left:B.left,top:B.top,width:l,height:m},horizontal:c<0?"left":b>0?"right":"center",vertical:f<0?"top":d>0?"bottom":"middle"};p<l&&h(b+c)<p&&(i.horizontal="center"),q<m&&h(d+f)<q&&(i.vertical="middle"),g(h(b),h(c))>g(h(d),h(f))?i.important="horizontal":i.important="vertical",e.using.call(this,a,i)}),k.offset(a.extend(B,{using:j}))})},a.ui.position={fit:{left:function(a,b){var c,d=b.within,e=d.isWindow?d.scrollLeft:d.offset.left,f=d.width,h=a.left-b.collisionPosition.marginLeft,i=e-h,j=h+b.collisionWidth-f-e;b.collisionWidth>f?i>0&&j<=0?(c=a.left+i+b.collisionWidth-f-e,a.left+=i-c):j>0&&i<=0?a.left=e:i>j?a.left=e+f-b.collisionWidth:a.left=e:i>0?a.left+=i:j>0?a.left-=j:a.left=g(a.left-h,a.left)},top:function(a,b){var c,d=b.within,e=d.isWindow?d.scrollTop:d.offset.top,f=b.within.height,h=a.top-b.collisionPosition.marginTop,i=e-h,j=h+b.collisionHeight-f-e;b.collisionHeight>f?i>0&&j<=0?(c=a.top+i+b.collisionHeight-f-e,a.top+=i-c):j>0&&i<=0?a.top=e:i>j?a.top=e+f-b.collisionHeight:a.top=e:i>0?a.top+=i:j>0?a.top-=j:a.top=g(a.top-h,a.top)}},flip:{left:function(a,b){var c,d,e=b.within,f=e.offset.left+e.scrollLeft,g=e.width,i=e.isWindow?e.scrollLeft:e.offset.left,j=a.left-b.collisionPosition.marginLeft,k=j-i,l=j+b.collisionWidth-g-i,m="left"===b.my[0]?-b.elemWidth:"right"===b.my[0]?b.elemWidth:0,n="left"===b.at[0]?b.targetWidth:"right"===b.at[0]?-b.targetWidth:0,o=-2*b.offset[0];k<0?(c=a.left+m+n+o+b.collisionWidth-g-f,(c<0||c<h(k))&&(a.left+=m+n+o)):l>0&&(d=a.left-b.collisionPosition.marginLeft+m+n+o-i,(d>0||h(d)<l)&&(a.left+=m+n+o))},top:function(a,b){var c,d,e=b.within,f=e.offset.top+e.scrollTop,g=e.height,i=e.isWindow?e.scrollTop:e.offset.top,j=a.top-b.collisionPosition.marginTop,k=j-i,l=j+b.collisionHeight-g-i,m="top"===b.my[1],n=m?-b.elemHeight:"bottom"===b.my[1]?b.elemHeight:0,o="top"===b.at[1]?b.targetHeight:"bottom"===b.at[1]?-b.targetHeight:0,p=-2*b.offset[1];k<0?(d=a.top+n+o+p+b.collisionHeight-g-f,(d<0||d<h(k))&&(a.top+=n+o+p)):l>0&&(c=a.top-b.collisionPosition.marginTop+n+o+p-i,(c>0||h(c)<l)&&(a.top+=n+o+p))}},flipfit:{left:function(){a.ui.position.flip.left.apply(this,arguments),a.ui.position.fit.left.apply(this,arguments)},top:function(){a.ui.position.flip.top.apply(this,arguments),a.ui.position.fit.top.apply(this,arguments)}}},function(){var b,c,d,e,g,h=document.getElementsByTagName("body")[0],i=document.createElement("div");b=document.createElement(h?"div":"body"),d={visibility:"hidden",width:0,height:0,border:0,margin:0,background:"none"},h&&a.extend(d,{position:"absolute",left:"-1000px",top:"-1000px"});for(g in d)b.style[g]=d[g];b.appendChild(i),c=h||document.documentElement,c.insertBefore(b,c.firstChild),i.style.cssText="position: absolute; left: 10.7432222px;",e=a(i).offset().left,f=e>10&&e<11,b.innerHTML="",c.removeChild(b)}()}(),a.ui.position});
/*!
 * jQuery UI Menu 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/menu/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./core","./widget","./position"],a):a(jQuery)}(function(a){return a.widget("ui.menu",{version:"1.11.4",defaultElement:"<ul>",delay:300,options:{icons:{submenu:"ui-icon-carat-1-e"},items:"> *",menus:"ul",position:{my:"left-1 top",at:"right top"},role:"menu",blur:null,focus:null,select:null},_create:function(){this.activeMenu=this.element,this.mouseHandled=!1,this.element.uniqueId().addClass("ui-menu ui-widget ui-widget-content").toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length).attr({role:this.options.role,tabIndex:0}),this.options.disabled&&this.element.addClass("ui-state-disabled").attr("aria-disabled","true"),this._on({"mousedown .ui-menu-item":function(a){a.preventDefault()},"click .ui-menu-item":function(b){var c=a(b.target);!this.mouseHandled&&c.not(".ui-state-disabled").length&&(this.select(b),b.isPropagationStopped()||(this.mouseHandled=!0),c.has(".ui-menu").length?this.expand(b):!this.element.is(":focus")&&a(this.document[0].activeElement).closest(".ui-menu").length&&(this.element.trigger("focus",[!0]),this.active&&1===this.active.parents(".ui-menu").length&&clearTimeout(this.timer)))},"mouseenter .ui-menu-item":function(b){if(!this.previousFilter){var c=a(b.currentTarget);c.siblings(".ui-state-active").removeClass("ui-state-active"),this.focus(b,c)}},mouseleave:"collapseAll","mouseleave .ui-menu":"collapseAll",focus:function(a,b){var c=this.active||this.element.find(this.options.items).eq(0);b||this.focus(a,c)},blur:function(b){this._delay(function(){a.contains(this.element[0],this.document[0].activeElement)||this.collapseAll(b)})},keydown:"_keydown"}),this.refresh(),this._on(this.document,{click:function(a){this._closeOnDocumentClick(a)&&this.collapseAll(a),this.mouseHandled=!1}})},_destroy:function(){this.element.removeAttr("aria-activedescendant").find(".ui-menu").addBack().removeClass("ui-menu ui-widget ui-widget-content ui-menu-icons ui-front").removeAttr("role").removeAttr("tabIndex").removeAttr("aria-labelledby").removeAttr("aria-expanded").removeAttr("aria-hidden").removeAttr("aria-disabled").removeUniqueId().show(),this.element.find(".ui-menu-item").removeClass("ui-menu-item").removeAttr("role").removeAttr("aria-disabled").removeUniqueId().removeClass("ui-state-hover").removeAttr("tabIndex").removeAttr("role").removeAttr("aria-haspopup").children().each(function(){var b=a(this);b.data("ui-menu-submenu-carat")&&b.remove()}),this.element.find(".ui-menu-divider").removeClass("ui-menu-divider ui-widget-content")},_keydown:function(b){var c,d,e,f,g=!0;switch(b.keyCode){case a.ui.keyCode.PAGE_UP:this.previousPage(b);break;case a.ui.keyCode.PAGE_DOWN:this.nextPage(b);break;case a.ui.keyCode.HOME:this._move("first","first",b);break;case a.ui.keyCode.END:this._move("last","last",b);break;case a.ui.keyCode.UP:this.previous(b);break;case a.ui.keyCode.DOWN:this.next(b);break;case a.ui.keyCode.LEFT:this.collapse(b);break;case a.ui.keyCode.RIGHT:this.active&&!this.active.is(".ui-state-disabled")&&this.expand(b);break;case a.ui.keyCode.ENTER:case a.ui.keyCode.SPACE:this._activate(b);break;case a.ui.keyCode.ESCAPE:this.collapse(b);break;default:g=!1,d=this.previousFilter||"",e=String.fromCharCode(b.keyCode),f=!1,clearTimeout(this.filterTimer),e===d?f=!0:e=d+e,c=this._filterMenuItems(e),c=f&&c.index(this.active.next())!==-1?this.active.nextAll(".ui-menu-item"):c,c.length||(e=String.fromCharCode(b.keyCode),c=this._filterMenuItems(e)),c.length?(this.focus(b,c),this.previousFilter=e,this.filterTimer=this._delay(function(){delete this.previousFilter},1e3)):delete this.previousFilter}g&&b.preventDefault()},_activate:function(a){this.active.is(".ui-state-disabled")||(this.active.is("[aria-haspopup='true']")?this.expand(a):this.select(a))},refresh:function(){var b,c,d=this,e=this.options.icons.submenu,f=this.element.find(this.options.menus);this.element.toggleClass("ui-menu-icons",!!this.element.find(".ui-icon").length),f.filter(":not(.ui-menu)").addClass("ui-menu ui-widget ui-widget-content ui-front").hide().attr({role:this.options.role,"aria-hidden":"true","aria-expanded":"false"}).each(function(){var b=a(this),c=b.parent(),d=a("<span>").addClass("ui-menu-icon ui-icon "+e).data("ui-menu-submenu-carat",!0);c.attr("aria-haspopup","true").prepend(d),b.attr("aria-labelledby",c.attr("id"))}),b=f.add(this.element),c=b.find(this.options.items),c.not(".ui-menu-item").each(function(){var b=a(this);d._isDivider(b)&&b.addClass("ui-widget-content ui-menu-divider")}),c.not(".ui-menu-item, .ui-menu-divider").addClass("ui-menu-item").uniqueId().attr({tabIndex:-1,role:this._itemRole()}),c.filter(".ui-state-disabled").attr("aria-disabled","true"),this.active&&!a.contains(this.element[0],this.active[0])&&this.blur()},_itemRole:function(){return{menu:"menuitem",listbox:"option"}[this.options.role]},_setOption:function(a,b){"icons"===a&&this.element.find(".ui-menu-icon").removeClass(this.options.icons.submenu).addClass(b.submenu),"disabled"===a&&this.element.toggleClass("ui-state-disabled",!!b).attr("aria-disabled",b),this._super(a,b)},focus:function(a,b){var c,d;this.blur(a,a&&"focus"===a.type),this._scrollIntoView(b),this.active=b.first(),d=this.active.addClass("ui-state-focus").removeClass("ui-state-active"),this.options.role&&this.element.attr("aria-activedescendant",d.attr("id")),this.active.parent().closest(".ui-menu-item").addClass("ui-state-active"),a&&"keydown"===a.type?this._close():this.timer=this._delay(function(){this._close()},this.delay),c=b.children(".ui-menu"),c.length&&a&&/^mouse/.test(a.type)&&this._startOpening(c),this.activeMenu=b.parent(),this._trigger("focus",a,{item:b})},_scrollIntoView:function(b){var c,d,e,f,g,h;this._hasScroll()&&(c=parseFloat(a.css(this.activeMenu[0],"borderTopWidth"))||0,d=parseFloat(a.css(this.activeMenu[0],"paddingTop"))||0,e=b.offset().top-this.activeMenu.offset().top-c-d,f=this.activeMenu.scrollTop(),g=this.activeMenu.height(),h=b.outerHeight(),e<0?this.activeMenu.scrollTop(f+e):e+h>g&&this.activeMenu.scrollTop(f+e-g+h))},blur:function(a,b){b||clearTimeout(this.timer),this.active&&(this.active.removeClass("ui-state-focus"),this.active=null,this._trigger("blur",a,{item:this.active}))},_startOpening:function(a){clearTimeout(this.timer),"true"===a.attr("aria-hidden")&&(this.timer=this._delay(function(){this._close(),this._open(a)},this.delay))},_open:function(b){var c=a.extend({of:this.active},this.options.position);clearTimeout(this.timer),this.element.find(".ui-menu").not(b.parents(".ui-menu")).hide().attr("aria-hidden","true"),b.show().removeAttr("aria-hidden").attr("aria-expanded","true").position(c)},collapseAll:function(b,c){clearTimeout(this.timer),this.timer=this._delay(function(){var d=c?this.element:a(b&&b.target).closest(this.element.find(".ui-menu"));d.length||(d=this.element),this._close(d),this.blur(b),this.activeMenu=d},this.delay)},_close:function(a){a||(a=this.active?this.active.parent():this.element),a.find(".ui-menu").hide().attr("aria-hidden","true").attr("aria-expanded","false").end().find(".ui-state-active").not(".ui-state-focus").removeClass("ui-state-active")},_closeOnDocumentClick:function(b){return!a(b.target).closest(".ui-menu").length},_isDivider:function(a){return!/[^\-\u2014\u2013\s]/.test(a.text())},collapse:function(a){var b=this.active&&this.active.parent().closest(".ui-menu-item",this.element);b&&b.length&&(this._close(),this.focus(a,b))},expand:function(a){var b=this.active&&this.active.children(".ui-menu ").find(this.options.items).first();b&&b.length&&(this._open(b.parent()),this._delay(function(){this.focus(a,b)}))},next:function(a){this._move("next","first",a)},previous:function(a){this._move("prev","last",a)},isFirstItem:function(){return this.active&&!this.active.prevAll(".ui-menu-item").length},isLastItem:function(){return this.active&&!this.active.nextAll(".ui-menu-item").length},_move:function(a,b,c){var d;this.active&&(d="first"===a||"last"===a?this.active["first"===a?"prevAll":"nextAll"](".ui-menu-item").eq(-1):this.active[a+"All"](".ui-menu-item").eq(0)),d&&d.length&&this.active||(d=this.activeMenu.find(this.options.items)[b]()),this.focus(c,d)},nextPage:function(b){var c,d,e;return this.active?void(this.isLastItem()||(this._hasScroll()?(d=this.active.offset().top,e=this.element.height(),this.active.nextAll(".ui-menu-item").each(function(){return c=a(this),c.offset().top-d-e<0}),this.focus(b,c)):this.focus(b,this.activeMenu.find(this.options.items)[this.active?"last":"first"]()))):void this.next(b)},previousPage:function(b){var c,d,e;return this.active?void(this.isFirstItem()||(this._hasScroll()?(d=this.active.offset().top,e=this.element.height(),this.active.prevAll(".ui-menu-item").each(function(){return c=a(this),c.offset().top-d+e>0}),this.focus(b,c)):this.focus(b,this.activeMenu.find(this.options.items).first()))):void this.next(b)},_hasScroll:function(){return this.element.outerHeight()<this.element.prop("scrollHeight")},select:function(b){this.active=this.active||a(b.target).closest(".ui-menu-item");var c={item:this.active};this.active.has(".ui-menu").length||this.collapseAll(b,!0),this._trigger("select",b,c)},_filterMenuItems:function(b){var c=b.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&"),d=new RegExp("^"+c,"i");return this.activeMenu.find(this.options.items).filter(".ui-menu-item").filter(function(){return d.test(a.trim(a(this).text()))})}})});
/*!
 * jQuery UI Autocomplete 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/autocomplete/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./core","./widget","./position","./menu"],a):a(jQuery)}(function(a){return a.widget("ui.autocomplete",{version:"1.11.4",defaultElement:"<input>",options:{appendTo:null,autoFocus:!1,delay:300,minLength:1,position:{my:"left top",at:"left bottom",collision:"none"},source:null,change:null,close:null,focus:null,open:null,response:null,search:null,select:null},requestIndex:0,pending:0,_create:function(){var b,c,d,e=this.element[0].nodeName.toLowerCase(),f="textarea"===e,g="input"===e;this.isMultiLine=!!f||!g&&this.element.prop("isContentEditable"),this.valueMethod=this.element[f||g?"val":"text"],this.isNewMenu=!0,this.element.addClass("ui-autocomplete-input").attr("autocomplete","off"),this._on(this.element,{keydown:function(e){if(this.element.prop("readOnly"))return b=!0,d=!0,void(c=!0);b=!1,d=!1,c=!1;var f=a.ui.keyCode;switch(e.keyCode){case f.PAGE_UP:b=!0,this._move("previousPage",e);break;case f.PAGE_DOWN:b=!0,this._move("nextPage",e);break;case f.UP:b=!0,this._keyEvent("previous",e);break;case f.DOWN:b=!0,this._keyEvent("next",e);break;case f.ENTER:this.menu.active&&(b=!0,e.preventDefault(),this.menu.select(e));break;case f.TAB:this.menu.active&&this.menu.select(e);break;case f.ESCAPE:this.menu.element.is(":visible")&&(this.isMultiLine||this._value(this.term),this.close(e),e.preventDefault());break;default:c=!0,this._searchTimeout(e)}},keypress:function(d){if(b)return b=!1,void(this.isMultiLine&&!this.menu.element.is(":visible")||d.preventDefault());if(!c){var e=a.ui.keyCode;switch(d.keyCode){case e.PAGE_UP:this._move("previousPage",d);break;case e.PAGE_DOWN:this._move("nextPage",d);break;case e.UP:this._keyEvent("previous",d);break;case e.DOWN:this._keyEvent("next",d)}}},input:function(a){return d?(d=!1,void a.preventDefault()):void this._searchTimeout(a)},focus:function(){this.selectedItem=null,this.previous=this._value()},blur:function(a){return this.cancelBlur?void delete this.cancelBlur:(clearTimeout(this.searching),this.close(a),void this._change(a))}}),this._initSource(),this.menu=a("<ul>").addClass("ui-autocomplete ui-front").appendTo(this._appendTo()).menu({role:null}).hide().menu("instance"),this._on(this.menu.element,{mousedown:function(b){b.preventDefault(),this.cancelBlur=!0,this._delay(function(){delete this.cancelBlur});var c=this.menu.element[0];a(b.target).closest(".ui-menu-item").length||this._delay(function(){var b=this;this.document.one("mousedown",function(d){d.target===b.element[0]||d.target===c||a.contains(c,d.target)||b.close()})})},menufocus:function(b,c){var d,e;return this.isNewMenu&&(this.isNewMenu=!1,b.originalEvent&&/^mouse/.test(b.originalEvent.type))?(this.menu.blur(),void this.document.one("mousemove",function(){a(b.target).trigger(b.originalEvent)})):(e=c.item.data("ui-autocomplete-item"),!1!==this._trigger("focus",b,{item:e})&&b.originalEvent&&/^key/.test(b.originalEvent.type)&&this._value(e.value),d=c.item.attr("aria-label")||e.value,void(d&&a.trim(d).length&&(this.liveRegion.children().hide(),a("<div>").text(d).appendTo(this.liveRegion))))},menuselect:function(a,b){var c=b.item.data("ui-autocomplete-item"),d=this.previous;this.element[0]!==this.document[0].activeElement&&(this.element.focus(),this.previous=d,this._delay(function(){this.previous=d,this.selectedItem=c})),!1!==this._trigger("select",a,{item:c})&&this._value(c.value),this.term=this._value(),this.close(a),this.selectedItem=c}}),this.liveRegion=a("<span>",{role:"status","aria-live":"assertive","aria-relevant":"additions"}).addClass("ui-helper-hidden-accessible").appendTo(this.document[0].body),this._on(this.window,{beforeunload:function(){this.element.removeAttr("autocomplete")}})},_destroy:function(){clearTimeout(this.searching),this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete"),this.menu.element.remove(),this.liveRegion.remove()},_setOption:function(a,b){this._super(a,b),"source"===a&&this._initSource(),"appendTo"===a&&this.menu.element.appendTo(this._appendTo()),"disabled"===a&&b&&this.xhr&&this.xhr.abort()},_appendTo:function(){var b=this.options.appendTo;return b&&(b=b.jquery||b.nodeType?a(b):this.document.find(b).eq(0)),b&&b[0]||(b=this.element.closest(".ui-front")),b.length||(b=this.document[0].body),b},_initSource:function(){var b,c,d=this;a.isArray(this.options.source)?(b=this.options.source,this.source=function(c,d){d(a.ui.autocomplete.filter(b,c.term))}):"string"==typeof this.options.source?(c=this.options.source,this.source=function(b,e){d.xhr&&d.xhr.abort(),d.xhr=a.ajax({url:c,data:b,dataType:"json",success:function(a){e(a)},error:function(){e([])}})}):this.source=this.options.source},_searchTimeout:function(a){clearTimeout(this.searching),this.searching=this._delay(function(){var b=this.term===this._value(),c=this.menu.element.is(":visible"),d=a.altKey||a.ctrlKey||a.metaKey||a.shiftKey;b&&(!b||c||d)||(this.selectedItem=null,this.search(null,a))},this.options.delay)},search:function(a,b){return a=null!=a?a:this._value(),this.term=this._value(),a.length<this.options.minLength?this.close(b):this._trigger("search",b)!==!1?this._search(a):void 0},_search:function(a){this.pending++,this.element.addClass("ui-autocomplete-loading"),this.cancelSearch=!1,this.source({term:a},this._response())},_response:function(){var b=++this.requestIndex;return a.proxy(function(a){b===this.requestIndex&&this.__response(a),this.pending--,this.pending||this.element.removeClass("ui-autocomplete-loading")},this)},__response:function(a){a&&(a=this._normalize(a)),this._trigger("response",null,{content:a}),!this.options.disabled&&a&&a.length&&!this.cancelSearch?(this._suggest(a),this._trigger("open")):this._close()},close:function(a){this.cancelSearch=!0,this._close(a)},_close:function(a){this.menu.element.is(":visible")&&(this.menu.element.hide(),this.menu.blur(),this.isNewMenu=!0,this._trigger("close",a))},_change:function(a){this.previous!==this._value()&&this._trigger("change",a,{item:this.selectedItem})},_normalize:function(b){return b.length&&b[0].label&&b[0].value?b:a.map(b,function(b){return"string"==typeof b?{label:b,value:b}:a.extend({},b,{label:b.label||b.value,value:b.value||b.label})})},_suggest:function(b){var c=this.menu.element.empty();this._renderMenu(c,b),this.isNewMenu=!0,this.menu.refresh(),c.show(),this._resizeMenu(),c.position(a.extend({of:this.element},this.options.position)),this.options.autoFocus&&this.menu.next()},_resizeMenu:function(){var a=this.menu.element;a.outerWidth(Math.max(a.width("").outerWidth()+1,this.element.outerWidth()))},_renderMenu:function(b,c){var d=this;a.each(c,function(a,c){d._renderItemData(b,c)})},_renderItemData:function(a,b){return this._renderItem(a,b).data("ui-autocomplete-item",b)},_renderItem:function(b,c){return a("<li>").text(c.label).appendTo(b)},_move:function(a,b){return this.menu.element.is(":visible")?this.menu.isFirstItem()&&/^previous/.test(a)||this.menu.isLastItem()&&/^next/.test(a)?(this.isMultiLine||this._value(this.term),void this.menu.blur()):void this.menu[a](b):void this.search(null,b)},widget:function(){return this.menu.element},_value:function(){return this.valueMethod.apply(this.element,arguments)},_keyEvent:function(a,b){this.isMultiLine&&!this.menu.element.is(":visible")||(this._move(a,b),b.preventDefault())}}),a.extend(a.ui.autocomplete,{escapeRegex:function(a){return a.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")},filter:function(b,c){var d=new RegExp(a.ui.autocomplete.escapeRegex(c),"i");return a.grep(b,function(a){return d.test(a.label||a.value||a)})}}),a.widget("ui.autocomplete",a.ui.autocomplete,{options:{messages:{noResults:"No search results.",results:function(a){return a+(a>1?" results are":" result is")+" available, use up and down arrow keys to navigate."}}},__response:function(b){var c;this._superApply(arguments),this.options.disabled||this.cancelSearch||(c=b&&b.length?this.options.messages.results(b.length):this.options.messages.noResults,this.liveRegion.children().hide(),a("<div>").text(c).appendTo(this.liveRegion))}}),a.ui.autocomplete});
/*
 * Thickbox 3.1 - One Box To Rule Them All.
 * By Cody Lindley (http://www.codylindley.com)
 * Copyright (c) 2007 cody lindley
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
*/

if ( typeof tb_pathToImage != 'string' ) {
    var tb_pathToImage = thickboxL10n.loadingAnimation;
}

/*!!!!!!!!!!!!!!!!! edit below this line at your own risk !!!!!!!!!!!!!!!!!!!!!!!*/

//on page load call tb_init
jQuery(document).ready(function(){
    tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
    imgLoader = new Image();// preload image
    imgLoader.src = tb_pathToImage;
});

/*
 * Add thickbox to href & area elements that have a class of .thickbox.
 * Remove the loading indicator when content in an iframe has loaded.
 */
function tb_init(domChunk){
    jQuery( 'body' )
        .on( 'click', domChunk, tb_click )
        .on( 'thickbox:iframe:loaded', function() {
            jQuery( '#TB_window' ).removeClass( 'thickbox-loading' );
        });
}

function tb_click(){
    var t = this.title || this.name || null;
    var a = this.href || this.alt;
    var g = this.rel || false;
    tb_show(t,a,g);
    this.blur();
    return false;
}

function tb_show(caption, url, imageGroup) {//function called when the user clicks on a thickbox link

    var $closeBtn;

    try {
        if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
            jQuery("body","html").css({height: "100%", width: "100%"});
            jQuery("html").css("overflow","hidden");
            if (document.getElementById("TB_HideSelect") === null) {//iframe to hide select elements in ie6
                jQuery("body").append("<iframe id='TB_HideSelect'>"+thickboxL10n.noiframes+"</iframe><div id='TB_overlay'></div><div id='TB_window' class='thickbox-loading'></div>");
                jQuery("#TB_overlay").click(tb_remove);
            }
        }else{//all others
            if(document.getElementById("TB_overlay") === null){
                jQuery("body").append("<div id='TB_overlay'></div><div id='TB_window' class='thickbox-loading'></div>");
                jQuery("#TB_overlay").click(tb_remove);
                jQuery( 'body' ).addClass( 'modal-open' );
            }
        }

        if(tb_detectMacXFF()){
            jQuery("#TB_overlay").addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
        }else{
            jQuery("#TB_overlay").addClass("TB_overlayBG");//use background and opacity
        }

        if(caption===null){caption="";}
        jQuery("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' width='208' /></div>");//add loader to the page
        jQuery('#TB_load').show();//show loader

        var baseURL;
        if(url.indexOf("?")!==-1){ //ff there is a query string involved
            baseURL = url.substr(0, url.indexOf("?"));
        }else{
            baseURL = url;
        }

        var urlString = /\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
        var urlType = baseURL.toLowerCase().match(urlString);

        if(urlType == '.jpg' || urlType == '.jpeg' || urlType == '.png' || urlType == '.gif' || urlType == '.bmp'){//code to show images

            TB_PrevCaption = "";
            TB_PrevURL = "";
            TB_PrevHTML = "";
            TB_NextCaption = "";
            TB_NextURL = "";
            TB_NextHTML = "";
            TB_imageCount = "";
            TB_FoundURL = false;
            if(imageGroup){
                TB_TempArray = jQuery("a[rel="+imageGroup+"]").get();
                for (TB_Counter = 0; ((TB_Counter < TB_TempArray.length) && (TB_NextHTML === "")); TB_Counter++) {
                    var urlTypeTemp = TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
                    if (!(TB_TempArray[TB_Counter].href == url)) {
                        if (TB_FoundURL) {
                            TB_NextCaption = TB_TempArray[TB_Counter].title;
                            TB_NextURL = TB_TempArray[TB_Counter].href;
                            TB_NextHTML = "<span id='TB_next'>&nbsp;&nbsp;<a href='#'>"+thickboxL10n.next+"</a></span>";
                        } else {
                            TB_PrevCaption = TB_TempArray[TB_Counter].title;
                            TB_PrevURL = TB_TempArray[TB_Counter].href;
                            TB_PrevHTML = "<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>"+thickboxL10n.prev+"</a></span>";
                        }
                    } else {
                        TB_FoundURL = true;
                        TB_imageCount = thickboxL10n.image + ' ' + (TB_Counter + 1) + ' ' + thickboxL10n.of + ' ' + (TB_TempArray.length);
                    }
                }
            }

            imgPreloader = new Image();
            imgPreloader.onload = function(){
                imgPreloader.onload = null;

                // Resizing large images - original by Christian Montoya edited by me.
                var pagesize = tb_getPageSize();
                var x = pagesize[0] - 150;
                var y = pagesize[1] - 150;
                var imageWidth = imgPreloader.width;
                var imageHeight = imgPreloader.height;
                if (imageWidth > x) {
                    imageHeight = imageHeight * (x / imageWidth);
                    imageWidth = x;
                    if (imageHeight > y) {
                        imageWidth = imageWidth * (y / imageHeight);
                        imageHeight = y;
                    }
                } else if (imageHeight > y) {
                    imageWidth = imageWidth * (y / imageHeight);
                    imageHeight = y;
                    if (imageWidth > x) {
                        imageHeight = imageHeight * (x / imageWidth);
                        imageWidth = x;
                    }
                }
                // End Resizing

                TB_WIDTH = imageWidth + 30;
                TB_HEIGHT = imageHeight + 60;
                jQuery("#TB_window").append("<a href='' id='TB_ImageOff'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/></a>" + "<div id='TB_caption'>"+caption+"<div id='TB_secondLine'>" + TB_imageCount + TB_PrevHTML + TB_NextHTML + "</div></div><div id='TB_closeWindow'><button type='button' id='TB_closeWindowButton'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><span class='tb-close-icon'></span></button></div>");

                jQuery("#TB_closeWindowButton").click(tb_remove);

                if (!(TB_PrevHTML === "")) {
                    function goPrev(){
                        if(jQuery(document).unbind("click",goPrev)){jQuery(document).unbind("click",goPrev);}
                        jQuery("#TB_window").remove();
                        jQuery("body").append("<div id='TB_window'></div>");
                        tb_show(TB_PrevCaption, TB_PrevURL, imageGroup);
                        return false;
                    }
                    jQuery("#TB_prev").click(goPrev);
                }

                if (!(TB_NextHTML === "")) {
                    function goNext(){
                        jQuery("#TB_window").remove();
                        jQuery("body").append("<div id='TB_window'></div>");
                        tb_show(TB_NextCaption, TB_NextURL, imageGroup);
                        return false;
                    }
                    jQuery("#TB_next").click(goNext);

                }

                jQuery(document).bind('keydown.thickbox', function(e){
                    if ( e.which == 27 ){ // close
                        tb_remove();

                    } else if ( e.which == 190 ){ // display previous image
                        if(!(TB_NextHTML == "")){
                            jQuery(document).unbind('thickbox');
                            goNext();
                        }
                    } else if ( e.which == 188 ){ // display next image
                        if(!(TB_PrevHTML == "")){
                            jQuery(document).unbind('thickbox');
                            goPrev();
                        }
                    }
                    return false;
                });

                tb_position();
                jQuery("#TB_load").remove();
                jQuery("#TB_ImageOff").click(tb_remove);
                jQuery("#TB_window").css({'visibility':'visible'}); //for safari using css instead of show
            };

            imgPreloader.src = url;
        }else{//code to show html

            var queryString = url.replace(/^[^\?]+\??/,'');
            var params = tb_parseQuery( queryString );

            TB_WIDTH = (params['width']*1) + 30 || 630; //defaults to 630 if no parameters were added to URL
            TB_HEIGHT = (params['height']*1) + 40 || 440; //defaults to 440 if no parameters were added to URL
            ajaxContentW = TB_WIDTH - 30;
            ajaxContentH = TB_HEIGHT - 45;

            if(url.indexOf('TB_iframe') != -1){// either iframe or ajax window
                urlNoQuery = url.split('TB_');
                jQuery("#TB_iframeContent").remove();
                if(params['modal'] != "true"){//iframe no modal
                    jQuery("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><button type='button' id='TB_closeWindowButton'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><span class='tb-close-icon'></span></button></div></div><iframe frameborder='0' hspace='0' allowtransparency='true' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;' >"+thickboxL10n.noiframes+"</iframe>");
                }else{//iframe modal
                    jQuery("#TB_overlay").unbind();
                    jQuery("#TB_window").append("<iframe frameborder='0' hspace='0' allowtransparency='true' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW + 29)+"px;height:"+(ajaxContentH + 17)+"px;'>"+thickboxL10n.noiframes+"</iframe>");
                }
            }else{// not an iframe, ajax
                if(jQuery("#TB_window").css("visibility") != "visible"){
                    if(params['modal'] != "true"){//ajax no modal
                        jQuery("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><button type='button' id='TB_closeWindowButton'><span class='screen-reader-text'>"+thickboxL10n.close+"</span><span class='tb-close-icon'></span></button></div></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
                    }else{//ajax modal
                        jQuery("#TB_overlay").unbind();
                        jQuery("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");
                    }
                }else{//this means the window is already up, we are just loading new content via ajax
                    jQuery("#TB_ajaxContent")[0].style.width = ajaxContentW +"px";
                    jQuery("#TB_ajaxContent")[0].style.height = ajaxContentH +"px";
                    jQuery("#TB_ajaxContent")[0].scrollTop = 0;
                    jQuery("#TB_ajaxWindowTitle").html(caption);
                }
            }

            jQuery("#TB_closeWindowButton").click(tb_remove);

            if(url.indexOf('TB_inline') != -1){
                jQuery("#TB_ajaxContent").append(jQuery('#' + params['inlineId']).children());
                jQuery("#TB_window").bind('tb_unload', function () {
                    jQuery('#' + params['inlineId']).append( jQuery("#TB_ajaxContent").children() ); // move elements back when you're finished
                });
                tb_position();
                jQuery("#TB_load").remove();
                jQuery("#TB_window").css({'visibility':'visible'});
            }else if(url.indexOf('TB_iframe') != -1){
                tb_position();
                jQuery("#TB_load").remove();
                jQuery("#TB_window").css({'visibility':'visible'});
            }else{
                var load_url = url;
                load_url += -1 === url.indexOf('?') ? '?' : '&';
                jQuery("#TB_ajaxContent").load(load_url += "random=" + (new Date().getTime()),function(){//to do a post change this load method
                    tb_position();
                    jQuery("#TB_load").remove();
                    tb_init("#TB_ajaxContent a.thickbox");
                    jQuery("#TB_window").css({'visibility':'visible'});
                });
            }

        }

        if(!params['modal']){
            jQuery(document).bind('keydown.thickbox', function(e){
                if ( e.which == 27 ){ // close
                    tb_remove();
                    return false;
                }
            });
        }

        $closeBtn = jQuery( '#TB_closeWindowButton' );
        /*
         * If the native Close button icon is visible, move focus on the button
         * (e.g. in the Network Admin Themes screen).
         * In other admin screens is hidden and replaced by a different icon.
         */
        if ( $closeBtn.find( '.tb-close-icon' ).is( ':visible' ) ) {
            $closeBtn.focus();
        }

    } catch(e) {
        //nothing here
    }
}

//helper functions below
function tb_showIframe(){
    jQuery("#TB_load").remove();
    jQuery("#TB_window").css({'visibility':'visible'}).trigger( 'thickbox:iframe:loaded' );
}

function tb_remove() {
    jQuery("#TB_imageOff").unbind("click");
    jQuery("#TB_closeWindowButton").unbind("click");
    jQuery( '#TB_window' ).fadeOut( 'fast', function() {
        jQuery( '#TB_window, #TB_overlay, #TB_HideSelect' ).trigger( 'tb_unload' ).unbind().remove();
        jQuery( 'body' ).trigger( 'thickbox:removed' );
    });
    jQuery( 'body' ).removeClass( 'modal-open' );
    jQuery("#TB_load").remove();
    if (typeof document.body.style.maxHeight == "undefined") {//if IE 6
        jQuery("body","html").css({height: "auto", width: "auto"});
        jQuery("html").css("overflow","");
    }
    jQuery(document).unbind('.thickbox');
    return false;
}

function tb_position() {
    var isIE6 = typeof document.body.style.maxHeight === "undefined";
    jQuery("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
    if ( ! isIE6 ) { // take away IE6
        jQuery("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
    }
}

function tb_parseQuery ( query ) {
    var Params = {};
    if ( ! query ) {return Params;}// return empty object
    var Pairs = query.split(/[;&]/);
    for ( var i = 0; i < Pairs.length; i++ ) {
        var KeyVal = Pairs[i].split('=');
        if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
        var key = unescape( KeyVal[0] );
        var val = unescape( KeyVal[1] );
        val = val.replace(/\+/g, ' ');
        Params[key] = val;
    }
    return Params;
}

function tb_getPageSize(){
    var de = document.documentElement;
    var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
    var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
    arrayPageSize = [w,h];
    return arrayPageSize;
}

function tb_detectMacXFF() {
    var userAgent = navigator.userAgent.toLowerCase();
    if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
        return true;
    }
}

var wpActiveEditor,send_to_editor;send_to_editor=function(a){var b,c="undefined"!=typeof tinymce,d="undefined"!=typeof QTags;if(wpActiveEditor)c&&(b=tinymce.get(wpActiveEditor));else if(c&&tinymce.activeEditor)b=tinymce.activeEditor,wpActiveEditor=b.id;else if(!d)return!1;if(b&&!b.isHidden()?b.execCommand("mceInsertContent",!1,a):d?QTags.insertContent(a):document.getElementById(wpActiveEditor).value+=a,window.tb_remove)try{window.tb_remove()}catch(e){}};var tb_position;!function(a){tb_position=function(){var b=a("#TB_window"),c=a(window).width(),d=a(window).height(),e=833<c?833:c,f=0;return a("#wpadminbar").length&&(f=parseInt(a("#wpadminbar").css("height"),10)),b.length&&(b.width(e-50).height(d-45-f),a("#TB_iframeContent").width(e-50).height(d-75-f),b.css({"margin-left":"-"+parseInt((e-50)/2,10)+"px"}),"undefined"!=typeof document.body.style.maxWidth&&b.css({top:20+f+"px","margin-top":"0"})),a("a.thickbox").each(function(){var b=a(this).attr("href");b&&(b=b.replace(/&width=[0-9]+/g,""),b=b.replace(/&height=[0-9]+/g,""),a(this).attr("href",b+"&width="+(e-80)+"&height="+(d-85-f)))})},a(window).resize(function(){tb_position()})}(jQuery);
!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d)if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);
