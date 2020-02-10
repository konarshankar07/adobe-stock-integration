/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiElement'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Magento_AdobeStockImageAdminUi/sorting',
            options: [],
            // eslint-disable-next-line max-len
            previewProvider: 'name = adobe_stock_images_listing.adobe_stock_images_listing.adobe_stock_images_columns.preview, ns = ${ $.ns }',
            applied: {},
            selectedOption: '',
            listens: {
                'selectedOption': 'applyChanges'
            },
            statefull: {
                selectedOption: true,
                applied: true
            },
            imports: {
                preparedOptions: '${ $.columnsProvider }:elems'
            },
            modules: {
                preview: '${ $.previewProvider }',
                columns: '${ $.columnsProvider }'
            },
            exports: {
                applied: '${ $.provider }:params.sorting'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            return this._super()
                .observe([
                    'applied',
                    'selectedOption'
                ]);
        },

        /**
         * Prepared sort order options
         */
        preparedOptions: function (columns) {
            if (columns && columns.length > 0) {
                columns.map(function (column) {
                    if (column.sortable === true) {
                        this.options.push({
                            value: column.index,
                            label: column.label
                        });
                    }
                }.bind(this));
            }
        },

        /**
         * Apply changes
         */
        applyChanges: function () {
            this.applied({
                field: this.selectedOption(),
                direction: 'desc'
            });
            //this.preview().hide();
        }
    });
});
