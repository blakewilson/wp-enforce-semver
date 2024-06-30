<?php
/**
Plugin Name: Alter Breaking Change Text 
Description: This plugin tests that the breaking change text filter works as expected
Version: 1.0.0
*/

add_filter( 'semantic_versioning_notice_text', function() {
  return '<br><br>Custom breaking change notice text';
});
