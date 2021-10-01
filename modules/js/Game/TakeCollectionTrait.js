

define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',

    g_gamethemeurl + 'modules/js/Core/ToolsTrait.js'
], function (dojo, declare) {
    return declare(
            'linko.TakeCollectionTrait',
            [
                common.ToolsTrait
            ],
            {

                constructor: function () {
                    this.debug('linko.TakeCollectionTrait constructor');
                },

                /* -------------------------------------------------------------
                 *                  BEGIN - Btn Actions
                 * ---------------------------------------------------------- */

                initalizeStealCards: function (args) {
                    this.debug('stel init', args.args.actualState.state_params.targetCollection);
                    dojo.query("#" + args.args.actualState.state_params.targetCollection).addClass("selected");

                    this.addActionButton('stealCard_button', _('Steal Cards'), 'onStealCard', null, false, 'blue');
                    this.addActionButton('discardCard_button', _('Discard Cards'), 'onDiscardCard', null, false, 'red');
                },

                onStealCard: function () {
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/stealCards.html", {
                        lock: true,
                        useraction: 'steal'
                    }, this, function (result) {
                        this.debug("Discard Card :", result);
                    }, function (is_error) {
                        //--error
                        this.debug("Play fail:", is_error);
                    });
                },

                onDiscardCard: function () {
                    this.ajaxcall("/" + this.game_name + "/" + this.game_name + "/stealCards.html", {
                        lock: true,
                        useraction: 'discard'
                    }, this, function (result) {
                        this.debug("Discard Card :", result);
                    }, function (is_error) {
                        //--error
                        this.debug("Play fail:", is_error);
                    });
                },

                /* -------------------------------------------------------------
                 *                  BEGIN - Notifications
                 * ---------------------------------------------------------- */

                notifStealCard: function (datas) {
                    this.debug('NSC', datas.args);

                },

                notifDiscardCard: function (datas) {
                    this.debug('NDC', datas.args);
                }

            });




});