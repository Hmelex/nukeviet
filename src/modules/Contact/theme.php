<?php

/**
 * NUKEVIET Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_MOD_CONTACT')) {
    exit('Stop!!!');
}

/**
 * main_theme()
 *
 * @param mixed $array_content
 * @param mixed $array_department
 * @param mixed $base_url
 * @param mixed $checkss
 * @return
 */
function contact_main_theme($array_content, $array_department, $catsName, $base_url, $checkss)
{
    global $module_info, $alias_url, $nv_Lang;

    $xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module);
    $xtpl->assign('GLANG', \NukeViet\Core\Language::$lang_global);
    $xtpl->assign('CHECKSS', $checkss);
    $xtpl->assign('CONTENT', $array_content);

    if (!empty($array_content['bodytext'])) {
        $xtpl->parse('main.bodytext');
    }

    if (!empty($array_department)) {
        foreach ($array_department as $dep) {
            if (empty($alias_url) and $dep['act'] == 2) {
                // Không hiển thị các bộ phận theo cấu hình trong quản trị
                continue;
            }

            $xtpl->assign('DEP', $dep);

            if (!empty($dep['note'])) {
                $xtpl->parse('main.dep.note');
            }

            if (!empty($dep['phone'])) {
                $nums = array_map('trim', explode('|', nv_unhtmlspecialchars($dep['phone'])));
                foreach ($nums as $k => $num) {
                    unset($m);
                    if (preg_match("/^(.*)\s*\[([0-9\+\.\,\;\*\#]+)\]$/", $num, $m)) {
                        $phone = ['number' => nv_htmlspecialchars($m[1]), 'href' => $m[2]];
                        $xtpl->assign('PHONE', $phone);
                        $xtpl->parse('main.dep.phone.item.href');
                        $xtpl->parse('main.dep.phone.item.href2');
                    } else {
                        $num = preg_replace("/\[[^\]]*\]/", '', $num);
                        $phone = ['number' => nv_htmlspecialchars($num)];
                        $xtpl->assign('PHONE', $phone);
                    }
                    if ($k) {
                        $xtpl->parse('main.dep.phone.item.comma');
                    }
                    $xtpl->parse('main.dep.phone.item');
                }

                $xtpl->parse('main.dep.phone');
            }
            if (!empty($dep['fax'])) {
                $xtpl->parse('main.dep.fax');
            }
            if (!empty($dep['email'])) {
                $emails = array_map('trim', explode(',', $dep['email']));
                foreach ($emails as $k => $email) {
                    $xtpl->assign('EMAIL', $email);
                    if ($k) {
                        $xtpl->parse('main.dep.email.item.comma');
                    }
                    $xtpl->parse('main.dep.email.item');
                }

                $xtpl->parse('main.dep.email');
            }

            if (!empty($dep['others'])) {
                $others = json_decode($dep['others'], true);

                if (!empty($others)) {
                    foreach ($others as $key => $value) {
                        if (!empty($value)) {
                            if (strtolower($key) == 'yahoo') {
                                $ys = array_map('trim', explode(',', $value));
                                foreach ($ys as $k => $y) {
                                    $xtpl->assign('YAHOO', ['name' => $key, 'value' => $y]);
                                    if ($k) {
                                        $xtpl->parse('main.dep.yahoo.item.comma');
                                    }
                                    $xtpl->parse('main.dep.yahoo.item');
                                }
                                $xtpl->parse('main.dep.yahoo');
                            } elseif (strtolower($key) == 'skype') {
                                $ss = array_map('trim', explode(',', $value));
                                foreach ($ss as $k => $s) {
                                    $xtpl->assign('SKYPE', ['name' => $key, 'value' => $s]);
                                    if ($k) {
                                        $xtpl->parse('main.dep.skype.item.comma');
                                    }
                                    $xtpl->parse('main.dep.skype.item');
                                }
                                $xtpl->parse('main.dep.skype');
                            } elseif (strtolower($key) == 'viber') {
                                $ss = array_map('trim', explode(',', $value));
                                foreach ($ss as $k => $s) {
                                    $xtpl->assign('VIBER', ['name' => $key, 'value' => $s]);
                                    if ($k) {
                                        $xtpl->parse('main.dep.viber.item.comma');
                                    }
                                    $xtpl->parse('main.dep.viber.item');
                                }
                                $xtpl->parse('main.dep.viber');
                            } elseif (strtolower($key) == 'icq') {
                                $ss = array_map('trim', explode(',', $value));
                                foreach ($ss as $k => $s) {
                                    $xtpl->assign('ICQ', ['name' => $key, 'value' => $s]);
                                    if ($k) {
                                        $xtpl->parse('main.dep.icq.item.comma');
                                    }
                                    $xtpl->parse('main.dep.icq.item');
                                }
                                $xtpl->parse('main.dep.icq');
                            } elseif (strtolower($key) == 'whatsapp') {
                                $ss = array_map('trim', explode(',', $value));
                                foreach ($ss as $k => $s) {
                                    $xtpl->assign('WHATSAPP', ['name' => $key, 'value' => $s]);
                                    if ($k) {
                                        $xtpl->parse('main.dep.whatsapp.item.comma');
                                    }
                                    $xtpl->parse('main.dep.whatsapp.item');
                                }
                                $xtpl->parse('main.dep.whatsapp');
                            } else {
                                $xtpl->assign('OTHER', ['name' => $key, 'value' => $value]);

                                if (nv_is_url($value)) {
                                    $xtpl->parse('main.dep.other.url');
                                } else {
                                    $xtpl->parse('main.dep.other.text');
                                }

                                $xtpl->parse('main.dep.other');
                            }
                        }
                    }
                }
            }

            $xtpl->parse('main.dep');
        }
    }

    $form = contact_form_theme($array_content, $catsName, $base_url, $checkss);
    $xtpl->assign('FORM', $form);

    $xtpl->parse('main');

    return $xtpl->text('main');
}

/**
 * contact_form_theme()
 *
 * @param mixed $array_content
 * @param mixed $catsName
 * @param mixed $base_url
 * @param mixed $checkss
 * @return
 */
function contact_form_theme($array_content, $catsName, $base_url, $checkss)
{
    global $module_info, $global_config, $nv_Lang;

    $xtpl = new XTemplate('form.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('CONTENT', $array_content);
    $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module);
    $xtpl->assign('GLANG', \NukeViet\Core\Language::$lang_global);
    $xtpl->assign('ACTION_FILE', $base_url);
    $xtpl->assign('CHECKSS', $checkss);

    if ($array_content['sendcopy']) {
        $xtpl->parse('main.sendcopy');
    }

    if ($global_config['captcha_type'] == 2) {
        $xtpl->assign('RECAPTCHA_ELEMENT', 'recaptcha' . nv_genpass(8));
        $xtpl->assign('N_CAPTCHA', $nv_Lang->getGlobal('securitycode1'));
        $xtpl->parse('main.recaptcha');
    } else {
        $xtpl->assign('GFX_WIDTH', NV_GFX_WIDTH);
        $xtpl->assign('GFX_HEIGHT', NV_GFX_HEIGHT);
        $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
        $xtpl->assign('CAPTCHA_REFRESH', $nv_Lang->getGlobal('captcharefresh'));
        $xtpl->assign('NV_GFX_NUM', NV_GFX_NUM);
        $xtpl->parse('main.captcha');
    }

    if (defined('NV_IS_USER')) {
        $xtpl->parse('main.iuser');
    } else {
        $xtpl->parse('main.iguest');
    }

    if (!empty($catsName)) {
        foreach ($catsName as $key => $cat) {
            $xtpl->assign('SELECTVALUE', $key);
            $xtpl->assign('SELECTNAME', $cat);
            $xtpl->parse('main.cats.select_option_loop');
        }
        $xtpl->parse('main.cats');
    }

    $xtpl->parse('main');

    return $xtpl->text('main');
}

/**
 * contact_sendcontact()
 *
 * @param mixed $row_id
 * @param mixed $fcat
 * @param mixed $ftitle
 * @param mixed $fname
 * @param mixed $femail
 * @param mixed $fphone
 * @param mixed $fcon
 * @param mixed $fpart
 * @param bool  $sendinfo
 * @return
 */
function contact_sendcontact($row_id, $fcat, $ftitle, $fname, $femail, $fphone, $fcon, $fpart, $sendinfo = true)
{
    global $global_config, $module_info, $array_department, $client_info, $nv_Lang;

    $xtpl = new XTemplate('sendcontact.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module);
    $xtpl->assign('SITE_NAME', $global_config['site_name']);
    $xtpl->assign('SITE_URL', $global_config['site_url']);
    $xtpl->assign('FULLNAME', $fname);
    $xtpl->assign('EMAIL', $femail);
    $xtpl->assign('PART', $array_department[$fpart]['full_name']);
    $xtpl->assign('IP', $client_info['ip']);
    $xtpl->assign('TITLE', $ftitle);
    $xtpl->assign('CONTENT', nv_htmlspecialchars($fcon));

    if ($sendinfo) {
        if (!empty($fcat)) {
            $xtpl->assign('CAT', $fcat);
            $xtpl->parse('main.sendinfo.cat');
        }

        if (!empty($fphone)) {
            $xtpl->assign('PHONE', $fphone);
            $xtpl->parse('main.sendinfo.phone');
        }
        $xtpl->parse('main.sendinfo');
    }

    $xtpl->parse('main');

    return $xtpl->text('main');
}
