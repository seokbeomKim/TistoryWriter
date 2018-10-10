<?php
namespace tistory_writer;

TistoryWriter::init();
wp_nonce_field( 'reference_meta_box', 'reference_nonce' );

include "metabox_main.php";
