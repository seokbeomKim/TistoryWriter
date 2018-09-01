<?php
namespace tistory_writer;

/* metabox debug */
TistoryWriter::init();

global $wp;
global $post;

wp_nonce_field( 'reference_meta_box', 'reference_nonce' );

// 페이지 로드
include "metabox_main.php";
