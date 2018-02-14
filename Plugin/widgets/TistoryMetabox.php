<?php
namespace tistory_writer;

const PLUGIN_SLUG = "Tistory Writer Widget";

// @codingStandardsIgnoreStart
class TistoryMetabox {
    function getContent() {
        Logger::log("showMetabox");

        $page_mgr = TistoryWriter::getManager(FEATURE_KEY\PAGE_LOADER);
        $page_mgr->getMetaboxPage();
    }

}
// @codingStandardsIgnoreEnd
