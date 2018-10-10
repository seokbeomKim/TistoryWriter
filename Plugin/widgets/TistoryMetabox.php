<?php
namespace tistory_writer;

class TistoryMetaBox {
    function getContent() {
        $page_mgr = TistoryWriter::getManager(FEATURE_KEY\PAGE_LOADER);
        $page_mgr->getMetaBoxPage();
    }
}