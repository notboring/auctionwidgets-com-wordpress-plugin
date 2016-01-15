<?php
/*
Plugin Name: AW eBay User profiles & Auctions
Plugin URI:  http://www.auctionwidgets.com/wordpress-plugin-en
Description: Display eBay auctions and eBay user profiles on your wordpress blog
Version:     1.0
Author:      Malte Köhrer
Author URI:  http://netzkomplex.de
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The auctionwidgets.com wordpress plugin is distributed in the hope
that it will be useful, but WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with AW eBay User profiles & Auctions. If not, see
https://www.gnu.org/licenses/gpl-2.0.html
*/

function aw_force_defaults($atts)
{
    return shortcode_atts(array(
        'type' => 'js',
        'lang' => 'en-GB',
        'site' => 'us',
        'color' => 'grey',
        'id' => '_empty_'
    ), $atts);
}

function aw_get_json($atts, $url_action, $div_id)
{
    $a = aw_force_defaults($atts);

    $html = "ID missing!";
    if ($a['id'] != "_empty_") {
        $html = '<script type="text/javascript" src="//api.auctionwidgets.com/' . $url_action . '/' . $a['type'] . '/' . $a['id'] . '/' . $a['color'] . '/' . $a['lang'] . '/' . $a['site'] . '"></script>';
        $html .= '<div id="' . $div_id . '" />';
    }
    return $html;
}

function aw_get_html($atts, $url_action)
{
    $a = aw_force_defaults($atts);
    $html = "ID missing!";
    if ($a['id'] != "_empty_") {
        $cache_id = "aw_com_" . $url_action . "_" . $a['type'] . '_' . $a['id'] . '_' . $a['color'] . '_' . $a['lang'] . '_' . $a['site'];
        $html = get_transient($cache_id);
        if ($html === false) {
            $html = file_get_contents("http://api.auctionwidgets.com/" . $url_action . '/' . $a['type'] . '/' . $a['id'] . '/' . $a['color'] . '/' . $a['lang'] . '/' . $a['site']);
            set_transient($cache_id, $html, 60 * 60);
        }
    }
    return $html;
}

// [ebayuser type="js" lang="de-DE" site="de" color="grey" id="ebay_user_name"]
function aw_ebayuser_func($atts)
{
    switch ($atts['type']) {
        case "js":
            return aw_get_json($atts, 'ebay-user-profile', 'auctionwidgets_com_user_profile');
        case "html":
            return aw_get_html($atts, 'ebay-user-profile');
    }
}

add_shortcode('ebayuser', 'aw_ebayuser_func');

// [ebayprofile type="js" lang="de-DE" site="de" color="grey" id="ebay_store_name"]
function aw_ebaylisting_func($atts)
{
    switch ($atts['type']) {
        case "js":
            return aw_get_json($atts, 'ebay-store-listing', 'auctionwidgets_com_store_listing');
        case "html":
            return aw_get_html($atts, 'ebay-store-listing');
    }
}

add_shortcode('ebaylisting', 'aw_ebaylisting_func');  
