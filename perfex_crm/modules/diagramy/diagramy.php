<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Diagramy
Module URI: https://codecanyon.net/item/diagramy-a-complete-diagram-editor-for-perfex-crm-flowcharts-process-diagrams-org-charts-more/29259040
Description: A complete diagram editor for Perfex CRM (Flowcharts, Process diagrams, Org Charts, UML, ER & Network Diagrams)
Version: 1.0.3
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
Requires at least: 3.0.*
*/
require_once __DIR__ . '/vendor/autoload.php';

define('DRAWIO_MODULE_NAME', 'diagramy');

modules\diagramy\core\Apiinit::the_da_vinci_code(DRAWIO_MODULE_NAME);
modules\diagramy\core\Apiinit::ease_of_mind(DRAWIO_MODULE_NAME);

hooks()->add_action('admin_init', 'diagramy_module_init_menu_items');
hooks()->add_action('admin_init', 'diagramy_permissions');
hooks()->add_filter('global_search_result_query', 'diagramy_global_search_result_query', 10, 3);
hooks()->add_filter('global_search_result_output', 'diagramy_global_search_result_output', 10, 2);
hooks()->add_filter('migration_tables_to_replace_old_links', 'diagramy_migration_tables_to_replace_old_links');

function diagramy_global_search_result_output($output, $data)
{
    if ('diagramy' == $data['type']) {
        $output = '<a href="' . admin_url('diagramy/preview/' . $data['result']['id']) . '">' . $data['result']['title'] . '</a>';
    }

    return $output;
}

function diagramy_global_search_result_query($result, $q, $limit)
{
    $CI_OBJECT = &get_instance();
    if (has_permission('diagramy', '', 'view')) {
        $CI_OBJECT->db->select()->from(db_prefix() . 'diagramy')->like('description', $q)->or_like('title', $q)->limit($limit);

        $CI_OBJECT->db->order_by('title', 'ASC');

        $result[] = [
            'result'         => $CI_OBJECT->db->get()->result_array(),
            'type'           => 'diagramy',
            'search_heading' => _l('diagramy'),
        ];
    }

    return $result;
}

function diagramy_migration_tables_to_replace_old_links($tables)
{
    $tables[] = [
        'table' => db_prefix() . 'diagramy',
    ];

    return $tables;
}

function diagramy_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('diagramy', $capabilities, _l('diagramy'));
}

// Register activation module hook
register_activation_hook(DRAWIO_MODULE_NAME, 'diagramy_module_activation_hook');

function diagramy_module_activation_hook()
{
    $CI_OBJECT = &get_instance();
    require_once __DIR__ . '/install.php';
}

// Register language files, must be registered if the module is using languages
register_language_files(DRAWIO_MODULE_NAME, [DRAWIO_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook.
 *
 * @return null
 */
function diagramy_module_init_menu_items()
{
    $CI_OBJECT = &get_instance();
    $CI_OBJECT->app_menu->add_sidebar_menu_item('diagramy_menu', [
        'name'     => 'diagramy', // The name if the item
        'href'     => admin_url('diagramy'), // URL of the item
        'position' => 10, // The menu position, see below for default positions.
        'icon'     => 'fa fa-area-chart', // Font awesome icon
    ]);


    if (is_admin()) {
        $CI_OBJECT->app_menu->add_setup_menu_item('diagramy', [
            'collapse' => true,
            'name'     => _l('diagramy'),
            'position' => 10,
        ]);

        $CI_OBJECT->app_menu->add_setup_children_item('diagramy', [
            'slug'     => 'diagramy-groups',
            'name'     => _l('diagramy_groups'),
            'href'     => admin_url('diagramy/groups'),
            'position' => 5,
        ]);
    }
}

$CI = &get_instance();
if ($CI->app_modules->is_active(DRAWIO_MODULE_NAME)) {
    hooks()->add_action('app_admin_footer', 'add_diagramy');
    function add_diagramy()
    {
        $CI        = &get_instance();
        $project_id = $CI->uri->segment(4);
        $CI->load->model(DRAWIO_MODULE_NAME . '/diagramy_model');
        $data = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'project', 'rel_id' => $project_id]);
        if (!empty($data)) {
?>
            <script type="text/javascript">
                $(function() {
                    if (typeof(project_overview_chart) != 'undefined') {
                        $(".project-overview-left .project-overview-table tbody").append(`<tr>
                            <td><?php echo _l('diagram'); ?></td>
                            <td><a href="<?php echo admin_url('diagramy/diagramy_create/') . $data['0']['id']; ?>"><?php echo $data['0']['title']; ?></a></td>
                            <tr>`);
                    }
                });
            </script>
            <?php
        }
        $CI        = &get_instance();
        $related_to = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'task', 'rel_id' => $project_id]);
        if (!empty($related_to)) {
            $data['diagramy'] = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'task', 'rel_id' => $project_id]);
            if (!empty($data)) {
            ?>
                <script type="text/javascript">
                    $(function() {
                        setTimeout(function() {
                            $(document).find('.task-info-total-logged-time').after(`
                                <div class="pull-left task-info">
                                <h5 class="no-margin"><i class="fa task-info-icon fa-fw fa-lg fa-pie-chart"></i><?php echo _l('diagram'); ?>:<span class="text-success"><a href="<?php echo admin_url('diagramy/diagramy_create/') . $data['diagramy']['0']['id']; ?>"><?php echo $data['diagramy']['0']['title']; ?></a></span>
                                </h5>
                                </div>
                                `);
                        }, 1000);
                    });
                </script>
        <?php
            }
        }
        ?>
        <script type="text/javascript">
            $(function() {
                $(document).on('click', '.main-tasks-table-href-name', function(event) {
                    onclick_str = $(this).attr('onclick');
                    related_id = onclick_str.split("(")[1].split(")")[0];
                    setTimeout(function() {
                        $.get(admin_url + "diagramy/get_data_by_task_id/" + related_id, function(data) {
                            $(document).find('.task-info-total-logged-time').after(data);
                        });
                    }, 100);
                });
            });
        </script>
        <?php
    }
}

$CI = &get_instance();
if ($CI->app_modules->is_active(DRAWIO_MODULE_NAME)) {
    hooks()->add_action('app_customers_footer', 'add_client_diagramy');
    function add_client_diagramy()
    {
        $CI        = &get_instance();
        $project_id = $CI->uri->segment(3);
        $CI->load->model(DRAWIO_MODULE_NAME . '/diagramy_model');
        $related_to = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'project', 'rel_id' => $project_id]);
        $data;
        if (!empty($related_to)) {
            $data['diagramy'] = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'project', 'rel_id' => $project_id]);
            if (!empty($data)) {
        ?>
                <script type="text/javascript">
                    $(function() {
                         $(".project-total-logged-hours").after(`<tr class="project-diagramy">
                            <td class="bold"><?php echo _l('diagram'); ?></td>
                            <td>: <a href="<?php echo site_url('diagramy/clients/clients_preview/') . $data['diagramy']['0']['id']; ?>" target="_blank"><?php echo $data['diagramy']['0']['title']; ?></a></td>
                            </tr>`);
                    });
                </script>
                <?php
            }
        }
        if ($CI->input->get('taskid')) {
            $task_id = $_GET['taskid'];
            $related_to = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'task', 'rel_id' => $task_id]);
            if (!empty($related_to)) {
                $data['task'] = $CI->diagramy_model->get_data_by_rel_id('diagramy', ['related_to' => 'task', 'rel_id' => $task_id]);
                if (!empty($data)) {
                ?>
                    <script type="text/javascript">
                        $(function() {
                            $("div.task-info.pull-left.text-danger").next('div.pull-left.task-info').after(`<div class="pull-left task-info project-diagramy">
                              <h5 class="no-margin"><i class="fa fa-pie-chart"></i>
                              <?php echo _l('diagram'); ?>:
                              <a href="<?php echo site_url('diagramy/clients/clients_preview/') . $data['task']['0']['id']; ?>" target="_blank" ><?php echo $data['task']['0']['title']; ?></a>
                              </h5>
                              </div>`);
                        });
                    </script>
<?php
                }
            }
        }
    }
}

hooks()->add_action('app_init', DRAWIO_MODULE_NAME . '_actLib');
function diagramy_actLib()
{
    $CI = &get_instance();
    $CI->load->library(DRAWIO_MODULE_NAME . '/Diagramy_aeiou');
    $envato_res = $CI->diagramy_aeiou->validatePurchase(DRAWIO_MODULE_NAME);
    if (!$envato_res) {
        set_alert('danger', 'One of your modules failed its verification and got deactivated. Please reactivate or contact support.');
    }
}

hooks()->add_action('pre_activate_module', DRAWIO_MODULE_NAME . '_sidecheck');
function diagramy_sidecheck($module_name)
{
    if (DRAWIO_MODULE_NAME == $module_name['system_name']) {
        modules\diagramy\core\Apiinit::activate($module_name);
    }
}

hooks()->add_action('pre_deactivate_module', DRAWIO_MODULE_NAME . '_deregister');
function diagramy_deregister($module_name)
{
    if (DRAWIO_MODULE_NAME == $module_name['system_name']) {
        delete_option(DRAWIO_MODULE_NAME . '_verification_id');
        delete_option(DRAWIO_MODULE_NAME . '_last_verification');
        delete_option(DRAWIO_MODULE_NAME . '_product_token');
        delete_option(DRAWIO_MODULE_NAME . '_heartbeat');
    }
}