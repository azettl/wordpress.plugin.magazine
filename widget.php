<?php

    class magazine_render_PDF_Widget extends WP_Widget {

        // Set up the widget name and description.
        public function __construct() {
            $widget_options = array(
                'classname' => 'magazine_render_PDF_Widget', 
                'description' => 'Displays a link to render a PDF from the current Page or Post.'
            );

            parent::__construct(
                'magazine_render_PDF_Widget', 
                'Magazine Render PDF', 
                $widget_options
            );
        }

        // Create the widget output.
        public function widget($args, $instance) {
            global $wp;

            $text = $instance['text'] ? esc_attr($instance['text']) : 'Render PDF';

            $sCurrentURL = home_url($wp->request);
            echo '<a href="' . $sCurrentURL . '?renderPDF=true">' . $text . '</a>';
        }

        // Create widget settings.
        public function form($instance) {
            $text       = $instance['text'] ? esc_attr($instance['text']) : 'Render PDF';
            $fieldname  = $this->get_field_name('text');

            echo '<p>
                    <label for="magazine_widget_option_text">Text</label> 
                    <input 
                        class="widefat" 
                        id="magazine_widget_option_text" 
                        name="' . $fieldname . '" 
                        type="text" 
                        value="' . $text . '" />
                </p>';
        }

        // Update widget settings.
        public function update($new_instance, $old_instance) {
            $instance = array();
            $instance['text'] = (!empty($new_instance['text'])) 
                ? 
                strip_tags($new_instance['text']) 
                : 
                '';

            return $instance;
        }
    }

    // Register the widget.
    add_action('widgets_init', function(){
        register_widget('magazine_render_PDF_Widget');
    });

    // Render Frontend PDF
    add_action('wp', function(){
        if ($_GET['renderPDF'] == "true") {
            global $post;

            if($post->ID > 0 && $_GET['renderPDF'] == 'true'){
                $aRenderResult = magazine_pdf::_renderPDF([$post->ID]);
                $http_status   = $aRenderResult['status_code'];
                $pdfContent    = $aRenderResult['content'];

                if($http_status == 200){
                    header('Content-Type: application/pdf');
                    echo $pdfContent;
                }else{
                    echo $pdfContent;
                }
                exit;
            }else{
                echo 'No valid post ID for PDF generation.';
            }
            exit; 
        }   
    });