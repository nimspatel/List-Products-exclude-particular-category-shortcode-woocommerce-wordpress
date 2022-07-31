<?php

function product_list( $atts ) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'tax_query'            => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug', // Or 'name' or 'term_id'
                'terms'    => array('custom'),
                'operator' => 'NOT IN', // Excluded
            )
        )
        // 'product_cat'    => 'hoodies'
    );

    if(isset($atts['id']) && !empty($atts['id'])){
        $pro_id_array = explode(",",$atts['id']);
    }

    $args['post__in'] = $pro_id_array;

    $loop = new WP_Query( $args );
    ob_start();
    ?>
   
    <div class="row">
      
    <?php
    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        $product_id = get_the_id();
        $variation_product = wc_get_product( $product_id );

        if( !$product->is_type('variable') ){
            continue;
        }
        
        $regular_price = $product->get_regular_price();
        $sale_price = $variation_product->get_sale_price();
        $pprice = $variation_product->get_price();
        $variations = $variation_product->get_available_variations();
        $variations_id = wp_list_pluck( $variations, 'variation_id' );
                // print_r($variations);

        ?>
            <div class="col-4">
                  <!-- <img width="612" height="408" src="" alt="" loading="lazy">   -->
                  <?php woocommerce_get_product_thumbnail(); ?>
                  <p class="producttitle">
                    <a href="<?php echo get_permalink(); ?>">
                    <?php echo woocommerce_get_product_thumbnail().' '.get_the_title();?>
                    </a>
                 </p>  

                <div class="flex_button">
                <?php

                     foreach($variations as $vari){
                        //Get the variation ID
                        $varid = $vari['variation_id'];
                        $varname = $vari['attributes']['attribute_quantity'];
                        $display_price = $vari['price_html'];
                        
                        // //Get the image ID from the variation ID
                        // $variation = new WC_Product_Variation( $varid );
                        // $image_id = $variation->get_image_id();
                        
                        // echo do_shortcode( '[add_to_cart id='.$varid.']' );
                        // echo do_shortcode( '[add_to_cart_url id='.$varid.']' );
                        if(!empty($varid) && isset($varid)){
                        echo '<p class="product woocommerce add_to_cart_inline ">';
                        echo '<a href="'.do_shortcode( '[add_to_cart_url id='.$varid.']' ).'" data-quantity="1" class="button product_type_variation add_to_cart_button ajax_add_to_cart" data-product_id="'.$varid.'" data-product_sku="" aria-label="Add “Rocky Road test - One Dozen” to your cart" rel="nofollow">'.$varname.''.$display_price.'</a>';
                        echo "</p>";
                        }

                    }


                    // foreach ($variations_id as $key => $value) {
                    //     if(!empty($value) && isset($value)){
                           
                    //         $product = get_product($value);
                    //         echo '<p class="product woocommerce add_to_cart_inline ">';
                    //         echo '<a href="'.$product->add_to_cart_url().'" data-quantity="1" class="button product_type_variation add_to_cart_button ajax_add_to_cart" data-product_id="8766" data-product_sku="" aria-label="Add “Rocky Road test - One Dozen” to your cart" rel="nofollow">Add to cart</a>';
                    //         echo "</p>";
                    //     }
           
                    // }

                ?> 
                </div>  
       </div>

    <?php
    endwhile;
    ?>          
    </div>
    
    <?php
    wp_reset_query();
   $content = ob_get_clean(); 
     return $content ;

}
add_shortcode( 'product_list', 'product_list' );
