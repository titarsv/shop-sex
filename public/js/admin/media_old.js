jQuery(document).ready(function($){
    let media = {};
    media.initialize = function( models, options ) {
        var allowed;

        options = options || {};
        Attachments.prototype.initialize.apply( this, arguments );

        this.args     = options.args;
        this._hasMore = true;
        this.created  = new Date();

        this.filters.order = function( attachment ) {
            var orderby = this.props.get('orderby'),
                order = this.props.get('order');

            if ( ! this.comparator ) {
                return true;
            }

            // We want any items that can be placed before the last
            // item in the set. If we add any items after the last
            // item, then we can't guarantee the set is complete.
            if ( this.length ) {
                return 1 !== this.comparator( attachment, this.last(), { ties: true });

                // Handle the case where there are no items yet and
                // we're sorting for recent items. In that case, we want
                // changes that occurred after we created the query.
            } else if ( 'DESC' === order && ( 'date' === orderby || 'modified' === orderby ) ) {
                return attachment.get( orderby ) >= this.created;

                // If we're sorting by menu order and we have no items,
                // accept any items that have the default menu order (0).
            } else if ( 'ASC' === order && 'menuOrder' === orderby ) {
                return attachment.get( orderby ) === 0;
            }

            // Otherwise, we don't want any items yet.
            return false;
        };

        // Observe the central `wp.Uploader.queue` collection to watch for
        // new matches for the query.
        //
        // Only observe when a limited number of query args are set. There
        // are no filters for other properties, so observing will result in
        // false positives in those queries.
        allowed = [ 's', 'order', 'orderby', 'posts_per_page', 'post_mime_type', 'post_parent', 'author' ];
        if ( wp.Uploader && _( this.args ).chain().keys().difference( allowed ).isEmpty().value() ) {
            this.observe( wp.Uploader.queue );
        }
    };

    media.hasMore = function() {
        return this._hasMore;
    };

    media.more = function( options ) {
        var query = this;

        // If there is already a request pending, return early with the Deferred object.
        if ( this._more && 'pending' === this._more.state() ) {
            return this._more;
        }

        if ( ! this.hasMore() ) {
            return jQuery.Deferred().resolveWith( this ).promise();
        }

        options = options || {};
        options.remove = false;

        return this._more = this.fetch( options ).done( function( resp ) {
            if ( _.isEmpty( resp ) || -1 === this.args.posts_per_page || resp.length < this.args.posts_per_page ) {
                query._hasMore = false;
            }
        });
    };

    media.sync = function( method, model, options ) {
        var args, fallback;

        // Overload the read method so Attachment.fetch() functions correctly.
        if ( 'read' === method ) {
            options = options || {};
            options.context = this;
            options.data = _.extend( options.data || {}, {
                action:  'query-attachments',
                post_id: wp.media.model.settings.post.id
            });

            // Clone the args so manipulation is non-destructive.
            args = _.clone( this.args );

            // Determine which page to query.
            if ( -1 !== args.posts_per_page ) {
                args.paged = Math.round( this.length / args.posts_per_page ) + 1;
            }

            options.data.query = args;
            return wp.media.ajax( options );

            // Otherwise, fall back to Backbone.sync()
        } else {
            /**
             * Call wp.media.model.Attachments.sync or Backbone.sync
             */
            fallback = Attachments.prototype.sync ? Attachments.prototype : Backbone;
            return fallback.sync.apply( this, arguments );
        }
    }
});