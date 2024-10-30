<?php
/*
  Plugin Name: Moly Olvasas
  Plugin URI: http://wordpress.org/extend/plugins/moly-olvasas/
  Description: With Moly olvasas widget you can add your moly.hu reading list to your wordpress blog.
  Version: 1.2
  Author: bolint
  Author URI: http://bolint.hu

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function Moly_Olvasas_Widget_install() {
    $widgetoptions = get_option('Moly_Olvasas_widget');
    $newoptions['nick'] = 'user';
    $newoptions['cover'] = 0;
    add_option('Moly_Olvasas_widget', $newoptions);
}

function Moly_Olvasas_Widget_init($content) {
    if (strpos($content, '[Moly_Olvasas-Widget]') === false) {
        return $content;
    } else {
        $code = Moly_Olvasas_Widget_createjscode(false);
        $content = str_replace('[Moly_Olvasas-Widget]', $code, $content);
        return $content;
    }
}

function Moly_Olvasas_Widget_insert() {
    echo Moly_Olvasas_Widget_createjscode(false);
}

function Moly_Olvasas_Widget_createjscode($widget) {
    if ($widget != true) {
        
    } else {
        $options = get_option('Moly_Olvasas_widget');
    }

    $jstag = '<script src="http://moly.hu/tagok/' . $options['nick'] . '/jelenlegi.js?r=' . date('ymd') . (intval($options['cover']) == 1 ? '' : '&cover=0') . '" type="text/javascript"></script>';
    return $jstag;
}

function Moly_Olvasas_Widget_uninstall() {
    delete_option('Moly_Olvasas_widget');
}

function widget_init_Moly_Olvasas_Widget_widget() {
    if (!function_exists('register_sidebar_widget'))
        return;

    function Moly_Olvasas_Widget_widget($args) {
        extract($args);
        $options = get_option('Moly_Olvasas_widget');
        ?>
        <?php echo $before_widget; ?>	
        <?php
        if (!stristr($_SERVER['PHP_SELF'], 'widgets.php')) {
            echo Moly_Olvasas_Widget_createjscode(true);
        }
        ?>
        <?php echo $after_widget; ?>
        <?php
    }

    function Moly_Olvasas_Widget_widget_control() {
        $options = $newoptions = get_option('Moly_Olvasas_widget');
        if ($_POST["Moly_Olvasas_widget_submit"]) {
            $newoptions['nick'] = strip_tags(stripslashes($_POST["Moly_Olvasas_widget_nick"]));
            $newoptions['cover'] = intval($_POST["Moly_Olvasas_widget_cover"]);
        }
        if ($options != $newoptions) {
            $options = $newoptions;
            update_option('Moly_Olvasas_widget', $options);
        }
        $nick = attribute_escape($options['nick']);
        $cover = intval(attribute_escape($options['cover']));
        ?>
        <p><label for="Moly_Olvasas_widget_nick"><?php _e('Felhasználónév'); ?>: <input class="widefat" id="Moly_Olvasas_widget_nick" name="Moly_Olvasas_widget_nick" type="text" value="<?php echo $nick; ?>" /></label></p>
        <p><label for="Moly_Olvasas_widget_cover"><?php _e('Borító megjelenítés'); ?>: <select class="widefat" id="Moly_Olvasas_widget_cover" name="Moly_Olvasas_widget_cover">
                    <option value="1"<?php echo $cover == 1 ? ' selected="selected"' : ''; ?>>igen</option>
                    <option value="0"<?php echo $cover != 1 ? ' selected="selected"' : ''; ?>>nem</option>
                </select></label></p>
        <input type="hidden" id="Moly_Olvasas_widget_submit" name="Moly_Olvasas_widget_submit" value="1" />
        <?php
    }

    register_sidebar_widget("Moly olvasás", Moly_Olvasas_Widget_widget);
    register_widget_control("Moly olvasás", "Moly_Olvasas_Widget_widget_control");
}

add_action('widgets_init', 'widget_init_Moly_Olvasas_Widget_widget');
add_filter('the_content', 'Moly_Olvasas_Widget_init');
register_activation_hook(__FILE__, 'Moly_Olvasas_Widget_install');
register_deactivation_hook(__FILE__, 'Moly_Olvasas_Widget_uninstall');
