<?php


/**
 * Класс Get со статическими методами для получения данных
 *
 */ 
class Get {

    /**
     * Получить фразы по группе для данного героя 
     *
     * @param string $hero имя героя (с пробелом)
     * @return array
    */
    public static function group_responses(string $hero, string $group) {
        $hero = str_replace(' ', '_', $hero);
        $url = 'https://dota2.fandom.com/ru/wiki/' . $hero . '/%D0%A0%D0%B5%D0%BF%D0%BB%D0%B8%D0%BA%D0%B8';
        $xpath = Get::_xpath($url);
        $startnode = $xpath->query("//div[@class='mw-parser-output']/h2[contains(., '$group')]")->item(0);
        $endnode = $startnode->nextElementSibling;
        while ($endnode && $endnode->tagName != 'h2') {
            $endnode = $endnode->nextElementSibling;
        }
        $group_responses = [];
        for ($node = $startnode; $node && $node !== $endnode; $node = $node->nextElementSibling) {
            if ($node instanceof DOMElement && $node->tagName == 'ul') {
                $lis = array_filter(
                    iterator_to_array($node->childNodes),
                    fn ($n) => $n instanceof DOMElement && $n->tagName == 'li'
                );
                $part = array_map(
                    fn ($li) => [
                        substr($li->textContent, 11),
                        $li->firstElementChild->firstElementChild->firstElementChild->getAttribute('src')
                    ],
                    $lis
                );             
                array_push($group_responses, $part);
            }
        }
        return array_merge(...$group_responses);
    }

    /**
     * Получить группы для данного героя 
     *
     * @param string $hero имя героя (с пробелом)
     * @return array
    */ 
    public static function groups(string $hero = 'Alchemist') {
        $hero = str_replace(' ', '_', $hero);
        $url = 'https://dota2.fandom.com/ru/wiki/' . $hero . '/%D0%A0%D0%B5%D0%BF%D0%BB%D0%B8%D0%BA%D0%B8';
        $xpath = Get::_xpath($url);
        $nodes = $xpath->query('//div[@class="mw-parser-output"]/h2');
        return array_map(fn ($n) => $n->childNodes->item(1)->textContent, iterator_to_array($nodes));
    }

    /**
     * Получить список героев
     *
     * @return array
    */ 
    public static function heroes() {
        $url = 'https://dota2.fandom.com/ru/wiki/%D0%93%D0%B5%D1%80%D0%BE%D0%B8';
        $xpath = Get::_xpath($url);
        $nodes = $xpath->query('//div[@style="position:relative;"]/a');
        $hs = array_map(fn ($n) => $n->getAttribute('title'), iterator_to_array($nodes));
        sort($hs);
        return $hs;    
    }

    /**
     * Шорткат для xpath по url
     *
     * @param string $url
     * @return DomXPath
    */ 
    private static function _xpath(string $url) {
        libxml_use_internal_errors(true);
        $dom = new DomDocument;
        $dom->loadHTMLFile($url);
        return new DomXPath($dom);
    }
}