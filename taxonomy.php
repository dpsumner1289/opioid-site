<?php
get_header();
$tax = get_queried_object()->taxonomy;
$term_name = get_queried_object()->name;
$term_id = get_queried_object()->term_id;
?>
<section class="resource_feed gtw">
    <h1 class="page-title">RESOURCES</h1>
    <div class="container container-sm">
        <?php resource_search(); ?>
        <div class="results-summary style"><?php echo $term_name; ?></div>
        <div class="subscribe">
            <a href="#" class="subscribe" data-type="topic" data-term="<?php echo $term_id; ?>">SUBSCRIBE TO THIS <?php echo strtoupper($tax); ?> <i class="fal fa-plus"></i></a>
        </div>
    </div>
    <div class="container container-md-lg flex row afs jfs resource_feed_html">
        
    </div>
    <?php // see ajax-loaders.php for this markup ?>
</section>
<?php echo sidebar_filters(); ?>
<script>
	jQuery(document).ready(function($){
		$.ajaxSetup({ cache: false });
        var page = 1;
        var queryArray = [];
        var keywords = $('.keywords').val();
        var baseHeader = '<?php echo $term_name; ?>'

        // loading state
        function stateChange(){
            var loading = '<div class="loading flex row full afc jfc"><i class="fas fa-spinner fa-pulse" style="padding-right:18px;"></i> Loading</div>';
            $('.resource_feed_html').html(loading);
        }

        // equalize listing heights
        function equalHeight() {
            $('div.resource_feed_html').each(function(){  
                var highestTitle = 0;
                $('.resource_title', this).each(function(){
                    if($(this).height() > highestTitle) {
                    highestTitle = $(this).height(); 
                    }
                });  
                $('.resource_title',this).height(highestTitle);
                var highestTax = 0;
                $('.resource_taxes', this).each(function(){
                    if($(this).height() > highestTax) {
                    highestTax = $(this).height(); 
                    }
                });  
                $('.resource_taxes',this).css('min-height', highestTax+'px');
            }); 
        }

        // build the Query
        function Query(tax, term, keywords="") {
            this.tax = tax;
            this.term = term;
            this.keywords = keywords;
        }

        // set a base Query for this tax/term
        var baseQuery = new Query('<?php echo $tax; ?>', <?php echo $term_id; ?>, '');
        queryArray.push(baseQuery);

        // save current search
        function save_search(query) {
            keywords = $('.keywords').val();
            if(keywords.length) {
                searchQuery = new Query('<?php echo $tax; ?>', <?php echo $term_id; ?>, keywords);
                query.push(searchQuery);
            }
            var dataSave = {
                action : 'otf_save_search',
                query : query,
                postsnum : 12,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataSave, function(response){
                $('.resource_feed_html').append(response);
                $('.save-confirmation').fadeOut(1500);
                keywords = '';
                queryArray = [];
                queryArray.push(baseQuery);
            });
        }

        // subscribe to current query
        function subscribe(query) {
            var dataSave = {
                action : 'otf_subscribe_to_this',
                query : query,
            }
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", dataSave, function(response){
                $('.resource_feed_html').append(response);
                $('.save-confirmation.successful').fadeOut(1500);
                $('.save-confirmation.exists').fadeOut(2700);
            });
        }

        // filter listings with AJAX call
        function resource_filter(thisQueryArray, whatKeywords = '', whatPage) {
            if(thisQueryArray.length) {
                thisQueryArray = thisQueryArray;
            }else {
                thisQueryArray = queryArray;
            }
            var datasearch = {
                action : 'resource_filter',
                keywords : whatKeywords,
                page : whatPage,
                query : thisQueryArray,
                postsnum : 12
            };
            $.post("<?php echo admin_url('admin-ajax.php'); ?>", datasearch, function(response){
                if(page > 1) {
                    $('.resource_feed_html').append(response);
                    page++;
                }else {
                    $('.resource_feed_html').html(response);
                    page++;
                }
                equalHeight();
            });
        }

        // taxonomy filter
        function tax_filter(clicked) {
            page = 1;
            stateChange();
            if(!clicked.parent('.term-wrap').hasClass('active')) {
                clicked.parent('.term-wrap').addClass('active');
                taxes = clicked.data('tax');
                terms = clicked.data('term');
                var thisQuery = new Query(taxes, terms);
                queryArray.push(thisQuery);
            }else {
                clicked.parent('.term-wrap').removeClass('active');
                for(var i = 0; i < queryArray.length; i++) {
                    if(queryArray[i].tax === clicked.data('tax') && queryArray[i].term === clicked.data('term')) {
                        var removeIndex = queryArray.indexOf(queryArray[i]);
                        queryArray.splice(removeIndex, 1);
                    }
                }
            }
            resource_filter(queryArray, keywords, page);
        }

        // reset filters
        function reset_query() {
            page = 1;
            keywords = '';
            queryArray = [];
            queryArray.push(baseQuery);
            $('.keywords').val('');
            $('.results-summary.style').html(baseHeader);
            $('a.subscribe').attr('data-type', 'topic');
            $('a.subscribe').html('SUBSCRIBE TO THIS <?php echo strtoupper($tax); ?> <i class="fal fa-plus"></i>');
            $('.term-wrap.active').removeClass('active');
            resource_filter(queryArray, keywords, page);
        }

        // search resources
        function resource_search(searchform) {
            stateChange();
            page = 1;
            keywords = searchform.closest('form').find($('.keywords')).val();

            // maybe search keywords
            if(keywords.length) {
                $('.results-summary').html('Search results for "<span>'+keywords+'</span>"');
                $('.results-summary:not(.style)').addClass('style');
                resource_filter(queryArray, keywords, page);
                $('a.subscribe').html('SAVE SEARCH <i class="fal fa-plus"></i>');
                $('a.subscribe').attr('data-type', 'search');
            }else {
                queryArray = [];
                queryArray.push(baseQuery);
                $('.results-summary.style').html(baseHeader);
                $('a.subscribe').html('SUBSCRIBE TO THIS <?php echo strtoupper($tax); ?> <i class="fal fa-plus"></i>');
                $('a.subscribe').attr('data-type', 'topic');
                resource_filter(queryArray, keywords, page);
            }
        }

        // initial Query based on taxonomy and term
        resource_filter(queryArray, keywords, page);

        // filter by taxonomy
        $(document).on('click', 'a.tax_term', function(e){
            e.preventDefault();
            tax_filter($(this));
        });

        // reset filtesr and search
        $(document).on('click', 'a.reset', function(e){
            e.preventDefault();
            reset_query();
        });

        // search posts
        $(document).on('click', '.post-search', function(e){
            e.preventDefault();
            resource_search($(this));
        });

        // load more posts
        $(document).on('click', 'a.moreposts', function(e){
            e.preventDefault();
            $(this).remove();
            resource_filter(queryArray, keywords, page);
        });

        // save current search
        $(document).on('click', 'a[data-type="search"]', function(e){
            e.preventDefault();
            save_search(queryArray);
        });

         // subscribe to current query
         $(document).on('click', 'a.subscribe:not([data-type="search"])', function(e){
            e.preventDefault();
            subQuery = new Query($(this).data('type'), $(this).data('term'));
            subscribe(subQuery);
        });
	});
</script>
<?php
get_footer();
?>