jQuery( function( $ ) {

    'use strict';

    /******************************************************************
     * Admin
     * @type {{init: Admin.init, install: Admin.install}}
     * @since 1.0
     * @author Alex Cherniy
     */
    var Admin = {

        /**
         * Init
         */
        init: function () {

            this.install  = this.install( this )

        },

        /**
         * Install
         */
        install: function() {

            $( document ).on(
                'click',
                '.subscribeAdminTab a',
                this.tabs )

        },

        /**
         * Tabs
         * @param e
         */
        tabs: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                $tab = $this.attr('href')

            $('.subscribeAdminTab').find('.nav-tab-active').removeClass('nav-tab-active')
            $this.addClass('nav-tab-active')

            $('.subscribeAdminTabs').find('.active').removeClass('active')
            $($tab).addClass('active')

        },

    }

    Admin.init()

});
