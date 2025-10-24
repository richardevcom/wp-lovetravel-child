<?php
/**
 * Packages Widget Layout 2
 *
 * Custom layout template for nd-travel packages Elementor widget.
 * Duplicated from layout-1.php for customization in child theme.
 *
 * @package LoveTravelChild
 * @since   2.0.0
 */

//image
$nd_travel_image_id = get_post_thumbnail_id( $nd_travel_id );
$nd_travel_image_attributes = wp_get_attachment_image_src( $nd_travel_image_id, $packagesgrid_image_size );

if ( $nd_travel_image_attributes[0] == '' ) { $nd_travel_output_image = ''; }else{

	//sale
	$nd_travel_meta_box_promotion_price = get_post_meta( get_the_ID(), 'nd_travel_meta_box_promotion_price', true );
	if ( $nd_travel_meta_box_promotion_price != '' ) { 
		$nd_travel_meta_box_promo_price = '<span style="background-color:'.$nd_travel_meta_box_color.';" class="nd_travel_position_absolute nd_travel_top_20 nd_travel_right_20 nd_travel_color_white nd_travel_font_size_14 nd_travel_line_height_1_em nd_travel_padding_5_15 nd_travel_border_radius_15 nd_travel_letter_spacing_1">'.__('SALE','nd-travel').'</span>'; 
	}else{ 
		$nd_travel_meta_box_promo_price = ''; 
	}
  

  $nd_travel_output_image = '
  <div class="nd_travel_section nd_travel_position_relative">
    '.$nd_travel_meta_box_promo_price.'
    <a href="'.$nd_travel_permalink.'"><img alt="" class="nd_travel_section nd_travel_postgrid_packages_single_package_img nd_travel_border_radius_top_15_important" src="'.$nd_travel_image_attributes[0].'"></a>
  </div>
  ';

}
//end image



//get destination
$nd_travel_meta_box_destinations = get_post_meta( get_the_ID(), 'nd_travel_meta_box_destinations', true );
if ( $nd_travel_meta_box_destinations == '' ) {
    $nd_travel_destination = '';
}else{
    $nd_travel_destination_title = get_the_title($nd_travel_meta_box_destinations);
    $nd_travel_destination_permalink = get_permalink( $nd_travel_meta_box_destinations );

    $nd_travel_destination = '
    <div class="nd_travel_section nd_travel_margin_top_5">
      <i style="color:'.$nd_travel_meta_box_color.'" class="fas fa-map-marker-alt nd_travel_font_size_15 nd_travel_float_left nd_travel_margin_top_4 nd_travel_margin_right_15 nd_travel_margin_0"></i>
      <a class="nd_travel_color_6e6e6e" href="'.$nd_travel_destination_permalink.'"><p class="nd_travel_margin_0 nd_travel_padding_0">'.$nd_travel_destination_title.'</p></a>
    </div>';
}


//get price
$nd_travel_meta_box_price = get_post_meta( get_the_ID(), 'nd_travel_meta_box_price', true );
$nd_travel_meta_box_promotion_price = get_post_meta( get_the_ID(), 'nd_travel_meta_box_promotion_price', true );
$nd_travel_currency_position = get_option('nd_travel_currency_position');

if ( $nd_travel_currency_position == 0 ) {
    $nd_travel_currency_right_value = nd_travel_get_currency();
    $nd_travel_currency_left_value = '';
}else{
    $nd_travel_currency_left_value = nd_travel_get_currency();
    $nd_travel_currency_right_value = '';
}

if ( $nd_travel_meta_box_promotion_price == '' ) {
    $nd_travel_price_content = $nd_travel_currency_left_value.' '.$nd_travel_meta_box_price.' '.$nd_travel_currency_right_value;
}else{
    $nd_travel_price_content = $nd_travel_currency_left_value.' '.$nd_travel_meta_box_promotion_price.' '.$nd_travel_currency_right_value.' <span class="nd_travel_font_size_16 nd_travel_opacity_04 nd_travel_text_decoration_line_through">'.$nd_travel_currency_left_value.' '.$nd_travel_meta_box_price.' '.$nd_travel_currency_right_value.'</span>';
}

if ( $nd_travel_meta_box_price != 0 ) {

  $nd_travel_price_div_content = '
  <div class="nd_travel_section nd_travel_width_50_percentage nd_travel_text_align_right nd_travel_search_page_l3_price_content">
    <h5 class="nd_travel_margin_0 nd_travel_padding_0">'.__('From','nd-travel').'</h5>
    <h3 class="nd_travel_margin_0 nd_travel_padding_0">'.$nd_travel_price_content.'</h3>  
  </div>';

}




//tax duration
$nd_travel_terms_tax = get_the_terms( $nd_travel_id, 'nd_travel_cpt_1_tax_1' );
$nd_travel_terms_tax_content = '';
if ( $nd_travel_terms_tax != null ) {
  foreach( $nd_travel_terms_tax as $nd_travel_term_tax ){
    $nd_travel_terms_tax_content .= $nd_travel_term_tax->name.' ';
  }
}


//icons
$nd_travel_gallery_icon = '';
$nd_travel_map_icon = '';



//galllery icon and dialog
if ( $nd_travel_meta_box_featured_image_replace != '' ) { 
  $nd_travel_gallery_icon = '

  <script type="text/javascript">
    //<![CDATA[
    
    jQuery(document).ready(function() {

      //START masonry
      jQuery(function ($) {
        

        //dialog
        $( "#nd_travel_search_page_l3_contact_dialog_'.$nd_travel_id.'" ).dialog({
          
          autoOpen: false,
          dialogClass: "no-close",
          height: "auto",
          width: 900,
          classes: {
            "ui-dialog": "nd_travel_search_page_l3_contact_dialog nd_travel_max_width_100_percentage nd_travel_position_absolute"
          }
        });

        //show on click
        $( ".nd_travel_search_page_l3_contact_icon_'.$nd_travel_id.'" ).on( "click", function() {
          
          $( "body" ).addClass( "nd_travel_dialog_filter" );
          $("#nd_travel_search_page_l3_contact_dialog_'.$nd_travel_id.' .nd_travel_dialog_form_package_field").val("'.$nd_travel_title.' - '.__('ID','nd-travel').' : '.$nd_travel_id.'");
          $( "#nd_travel_search_page_l3_contact_dialog_'.$nd_travel_id.'" ).dialog( "open" );

        });

        $( "#nd_travel_dialog_contact_close_'.$nd_travel_id.'" ).on( "click", function() {
			$( "#nd_travel_search_page_l3_contact_dialog_'.$nd_travel_id.'" ).dialog( "close" );
			$( "body" ).removeClass( "nd_travel_dialog_filter" );
		});


      });
      //END masonry

    });

    //]]>
  </script>


  <i style="color:'.$nd_travel_meta_box_color.'" class="fas fa-envelope nd_travel_font_size_15 nd_travel_float_right nd_travel_margin_top_6 nd_travel_margin_left_15 nd_travel_margin_0 nd_travel_cursor_pointer nd_travel_search_page_l3_contact_icon_'.$nd_travel_id.'"></i>

  <div id="nd_travel_search_page_l3_contact_dialog_'.$nd_travel_id.'">
    
  '.do_shortcode($nd_travel_meta_box_featured_image_replace).'  

  <i class="fas fa-times nd_travel_dialog_close" style="background-color:'.$nd_travel_meta_box_color.'" id="nd_travel_dialog_contact_close_'.$nd_travel_id.'""></i>

  </div>

  '; 
}


//map icon and dialog
if ( $nd_travel_meta_box_tab_map_content != '' ) { 
  $nd_travel_map_icon = '
  <script type="text/javascript">
    //<![CDATA[
    
    jQuery(document).ready(function() {

      //START masonry
      jQuery(function ($) {
        

        //dialog
        $( "#nd_travel_search_page_l3_map_dialog_'.$nd_travel_id.'" ).dialog({
          
          autoOpen: false,
          dialogClass: "no-close",
          height: "auto",
          width: 900,
          classes: {
            "ui-dialog": "nd_travel_search_page_l3_map_dialog nd_travel_max_width_100_percentage nd_travel_position_absolute"
          }
        });

        //show on click
        $( ".nd_travel_search_page_l3_map_icon_'.$nd_travel_id.'" ).on( "click", function() {
          $( "body" ).addClass( "nd_travel_dialog_filter" );
          $( "#nd_travel_search_page_l3_map_dialog_'.$nd_travel_id.'" ).dialog( "open" );
        });

        $( "#nd_travel_dialog_map_close_'.$nd_travel_id.'" ).on( "click", function() {
			$( "#nd_travel_search_page_l3_map_dialog_'.$nd_travel_id.'" ).dialog( "close" );
			$( "body" ).removeClass( "nd_travel_dialog_filter" );
		});


      });
      //END masonry

    });

    //]]>
  </script>


  <i style="color:'.$nd_travel_meta_box_color.'" class="fas fa-route nd_travel_font_size_15 nd_travel_float_right nd_travel_margin_top_6 nd_travel_margin_left_15 nd_travel_margin_0 nd_travel_cursor_pointer nd_travel_search_page_l3_map_icon_'.$nd_travel_id.'"></i>

  <div id="nd_travel_search_page_l3_map_dialog_'.$nd_travel_id.'">
    
  '.$nd_travel_meta_box_tab_map_content.' 

  <i class="fas fa-times nd_travel_dialog_close" style="background-color:'.$nd_travel_meta_box_color.'" id="nd_travel_dialog_map_close_'.$nd_travel_id.'""></i>

  </div>


  '; 
}


/*START preview*/
echo '
<div id="nd_travel_elementor_packages_widget_l1_'.$nd_travel_id.'" class="nd_travel_elementor_packages_widget_l1 nd_travel_masonry_item '.$packages_width.' nd_travel_padding_15 nd_travel_width_100_percentage_responsive">

    <div class="nd_travel_section nd_travel_background_color_fff">

      '.$nd_travel_output_image.'

      <div class="nd_travel_margin_top_50_negative nd_travel_section nd_travel_padding_30 nd_travel_bg_white nd_travel_box_shadow_0_0_30_0_0001 nd_travel_border_radius_bottom_15">

        
        <!--info section-->          
        <div class="nd_travel_box_shadow_0_0_15_0_0001 nd_travel_package_preview_info_section nd_travel_bg_white nd_travel_section nd_travel_padding_10_20 nd_travel_border_radius_5 nd_travel_margin_bottom_30 nd_travel_z_index_9 nd_travel_position_relative">

          <div class="nd_travel_float_left nd_travel_width_60_percentage">
            <i style="color:'.$nd_travel_meta_box_color.'" class="fas fa-clock nd_travel_font_size_15 nd_travel_float_left nd_travel_margin_top_2 nd_travel_margin_right_15 nd_travel_margin_0"></i>
            <a class="nd_travel_color_6e6e6e" href="'.$nd_travel_permalink.'"><p class="nd_travel_font_size_14 nd_travel_margin_0 nd_travel_padding_0">'.$nd_travel_terms_tax_content.'</p></a>
          </div>

          <div class="nd_travel_float_left nd_travel_width_40_percentage">

            '.$nd_travel_map_icon.'
            '.$nd_travel_gallery_icon.'
            
          </div>

        </div>
        <!--info section--> 

        

        <!--title section-->
        <a href="'.$nd_travel_permalink.'"><h4 class="nd_travel_margin_0 nd_travel_padding_0">'.$nd_travel_title.'</h4></a>
        '.$nd_travel_destination.'
        <!--title section-->
        

        
        <!--description section-->
        <div class="nd_travel_section nd_travel_height_1 nd_travel_bg_grey_3 nd_travel_margin_top_20"></div>
        <p class="nd_travel_margin_top_20 nd_travel_margin_bottom_15 nd_travel_padding_0 nd_travel_section">'.$nd_travel_excerpt.'</p>
        <div class="nd_travel_section nd_travel_height_1 nd_travel_bg_grey_3 nd_travel_margin_top_5 nd_travel_margin_bottom_20"></div>
        <!--description section-->
        

        
        <!--footer section-->
        <div class="nd_travel_section">

            <div class="nd_travel_section nd_travel_width_50_percentage nd_travel_text_align_left nd_travel_padding_top_20 nd_travel_search_page_l3_button_content">

              <a class="nd_travel_color_white nd_travel_bg_red_hover nd_travel_padding_10_30 nd_travel_border_radius_5 nd_travel_font_size_14 nd_travel_font_weight_bold nd_travel_margin_top_15" style="background-color: '.$nd_travel_meta_box_color.';" href="'.$nd_travel_permalink.'">'.__('Details','nd-travel').'</a>   

            </div>

            '.$nd_travel_price_div_content.'

        </div>
        <!--footer section-->

        

      </div>
    </div>    
</div>';
/*END preview*/ 