<?php
namespace tistory_writer;

// @codingStandardsIgnoreStart
class TistoryMetabox {
    function getContent() {
        $page_mgr = TistoryWriter::getManager(FEATURE_KEY\PAGE_LOADER);
        $page_mgr->getMetaboxPage();
    }

}
// @codingStandardsIgnoreEnd
