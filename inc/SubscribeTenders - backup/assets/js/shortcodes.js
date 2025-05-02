jQuery( function( $ ) {

    'use strict';

    /******************************************************************
     * Cart
     * @type {{init: Shortcodes.init, install: Shortcodes.install}}
     * @since 1.0
     * @author Alex Cherniy
     */
    var Shortcodes = {

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
                'change',
                '.subscribeTenderGetRegion',
                this.load_regions )

            $( document ).on(
                'click',
                '.subscribeTenderRemoveGroup',
                this.remove_group )

            $( document ).on(
                'submit',
                '.subscribeTenderForm',
                this.submit )

            $( document ).on(
                'change',
                '.treakingTenderForm input:checkbox',
                this.treaking_tender )

            $( document ).on(
                'submit',
                '.treakingTenderForm',
                this.treaking_tender_submit )

            $( document ).on(
                'change',
                '.subscribeTenderFormEmail',
                this.get_subscribes_by_email )


        },

        /**
         * Regions
         * @param e
         */
        load_regions: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                $product = $this.parent().parent().find('.subscribeTenderProduct').val(),
                $country = $this.parent().parent().find('.subscribeTenderCountry').val()

            $.ajax( {
                beforeSend: function(xhr){

                },
                data: {
                    action: 'select_tender_region',
                    product: $product,
                    country: $country,
                },
                headers     :   {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                method: 'POST',
                complete: function(){

                },
                error: function(response) {

                    $this.find('.error').html(response.message)

                },
                success: function( response ) {

                    let $containerajax = $this.parent().parent().find('.ajaxTenderRegions'),
                        $row = $this.parent().parent().parent().find('.groupRow:first')

                    if( response.success === true )
                    {

                        $containerajax.html(response.data.html).promise().done(function()
                        {
                            $('#subscribe-form-input-regions').multiselect({
                                selectAll : false,
                                texts: {
                                    placeholder : null,
                                },
                                minHeight : 150,
                            })
                        })

                        $row.addClass('region_active')

                    }else{
                        $containerajax.html('')
                        $row.removeClass('region_active')
                    }

                },
                url: subscribe_tender.ajax_url
            } );

        },

        /**
         * Remove Group
         * @param e
         */
        remove_group: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                $rows = $this.data('rows'),
                $container = $('.ajaxSubscribesUser')

            $.ajax( {
                beforeSend: function(xhr)
                {

                    $container.addClass('preload')

                },
                data: {
                    action: 'remove_subscribe_group',
                    rows: $rows
                },
                headers     :   {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                method: 'POST',
                complete: function()
                {

                    $container.removeClass('preload')

                },
                error: function(response)
                {


                },
                success: function( response )
                {

                    $container.html(response.data.html)

                },
                url: subscribe_tender.ajax_url
            } );

        },

        /**
         * Regions
         * @param e
         */
        submit: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                $message = $this.find('.message'),
                err = false,
                $form = $('.subscribeTenderForm')

            $.ajax( {
                beforeSend: function(xhr)
                {

                    $this.addClass('preload')
                    $message.html('')
                },
                data: $this.serialize(),
                headers     :   {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                method: 'POST',
                complete: function()
                {

                    $this.removeClass('preload')

                },
                error: function(response)
                {

                    $message.html(response.message)

                },
                success: function( response )
                {

                    if( response.success === true )
                    {
                        $message.html(response.data.message)

                        $form.find('.ajaxTenderRegions').html('')
                        $form.find('select').prop('selectedIndex',0)
                        $form.find('.subscribeTenderRemoveGroup').removeAttr('style')
                        $form.find('.region_active').removeClass('region_active')

                    }else {
                        $message.html(response.data.message)
                    }

                    $('.ajaxSubscribesUser').html(response.data.html)

                },
                url: subscribe_tender.ajax_url
            } );

        },

        /**
         * Get Subscribes by Email User
         * @param e
         */
        get_subscribes_by_email: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                email = $this.val(),
                $container = $('.ajaxSubscribesUser')

            $.ajax( {
                beforeSend: function(xhr)
                {

                    $container.addClass('preload')

                },
                data: {
                    action: 'subscribes_by_email',
                    email: email
                },
                headers     :   {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                method: 'POST',
                complete: function()
                {

                    $container.removeClass('preload')

                },
                error: function(response)
                {

                },
                success: function( response )
                {

                    $('.ajaxSubscribesUser').html(response.data.html)

                },
                url: subscribe_tender.ajax_url
            } );

        },


        /**
         * Treaking Tender
         * @param e
         */
        treaking_tender: function (e)
        {

            e.preventDefault()

            let $this = $(this)

            $this.parents('.treakingTenderForm').trigger('submit')

        },

        /**
         * Treaking Tender Submit
         * @param e
         */
        treaking_tender_submit: function (e)
        {

            e.preventDefault()

            let $this = $(this),
                $message = $this.find('.message')

            $.ajax( {
                beforeSend: function(xhr)
                {

                    $this.addClass('preload')
                    $message.html('')
                },
                data: $this.serialize(),
                headers     :   {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                method: 'POST',
                complete: function()
                {

                    $this.removeClass('preload')

                },
                error: function(response)
                {

                    $message.html(response.message)

                },
                success: function( response )
                {

                    if( response.success === true )
                    {
                        $message.html(response.data.message)
                    }else {
                        $message.html(response.data.message)
                    }

                },
                url: subscribe_tender.ajax_url
            } );

        },

    }

    Shortcodes.init()

});
