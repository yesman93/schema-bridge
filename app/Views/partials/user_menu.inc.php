<?php








$is_public = $is_public ?? false;

/*
?>
    <div class="dropdown text-end" title="<?=UserModel::get_name()?>" data-bs-toggle="tooltip">
        <a href="javascript:;" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <?php

            $user = UserModel::get_user();
            $icon_fpath = $user['icon_fpath'] ?? false;
            $icon_fpath_html = $user['icon_fpath_html'] ?? false;
            if (!empty($icon_fpath) && file_exists($icon_fpath)) {
                ?><span class="d-inline-block bg-dark px-0 pt-1 me-1 text-center text-white align-middle rounded-circle d-inline-block" style="width:2rem;height:2rem;background-repeat: no-repeat; background-size: 100% auto; background-position: center center; background-image: url('<?=$icon_fpath_html?>');"></span><?php
            } else {
                $initials = $user['initials'] ?? false;
                ?><span class="d-inline-block bg-dark px-0 pt-1 me-1 text-center text-white align-middle rounded-circle" style="width:2rem;height:2rem"><?=$initials?></span><?php
            }

            ?>
            <span class="<?php echo ($is_public ? 'd-sm-inline d-none' : 'd-sm-inline d-none'); ?> align-middle"><?php echo UserModel::get_name(); ?></span>
        </a>
        <ul class="dropdown-menu text-small">
            <?php



            if (App\Models\UserModel::is_logged_as()) {
                ?>
                <li>
                    <a class="dropdown-item text-danger" href="/user/log_back2admin/"><i class="fas fa-arrow-circle-left me-1"></i><?php __t('Odhlásit jako uživatel'); ?></a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php
            }

            if ($this->_action != 'login_applications') {


                if (UserModel::is_role(RoleModel::ROLE_NAME_ADMIN)) {
                    ?><li><a class="dropdown-item text-danger" href="/admin/panel/"><i class="fas fa-user-cog me-1"></i><?php __t('Administrace'); ?></a></li><?php
                }


                if (UserModel::is_technician()) {

                    ?>
                    <li>
                        <?php
                        echo $this->elems->link2modal('/app/dev_tools/', '<span><i class="fas fa-code me-1"></i>' . __tx('Dev tools') . '</span><span class="mt-3 opacity-5"><i class="fas fa-wrench ms-4 fa-xs small"></i></span>', [
                            'class' => 'dropdown-item text-danger',
                        ]);
                        ?>
                    </li>
                    <?php
                }
                ?>

                <li><a class="dropdown-item" href="/user/detail/"><i class="fas fa-user me-1"></i><?php __t('Mé údaje'); ?></a></li>


                <?php
                // tikety jen tomu, kdo ma prava
                if ((new \Mark\Libs\Security\Authentication())->verify_action('bug_report', 'bug_reports')) {

                    ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <div class="row gx-1">
                            <div class="col-9">
                                <?php
                                $br_count_bubble = \App\Models\Bug_reportModel::get_bubble_count();
                                $br_bubble_class = empty($br_count_bubble) ? ' d-none' : false;
                                ?>
                                <a class="dropdown-item" href="/bug_report/bug_reports/"><i class="fas fa-user-headset me-1"></i><?php __t('Podpora'); ?><sup class="badge bg-dark text-white px-2 py-1 ms-1 me-n2 <?=$br_bubble_class?>"><?=$br_count_bubble?></sup></a>
                            </div>
                            <div class="col-3">
                                <?php

                                $add_br = $this->elems->link2modal('/bug_report/add/' . link2param(\Mark\Libs\Utilities\Http::getURI()) . '/', '<i class="fas fa-plus"></i>', array(
                                    'class' => 'dropdown-item',
                                    'title' => __tx('Přidat tiket'),
                                    'size' => 'modal-lg',
                                ));

                                echo $add_br;
                                ?>
                            </div>
                        </div>
                    </li>
                    <?php
                }
                ?>
                <li><hr class="dropdown-divider"></li>
                <?php
            }



            ?>
            <li><a class="dropdown-item" href="/user/logout/"><i class="fas fa-sign-out me-1"></i><?php __t('Odhlásit'); ?></a></li>
        </ul>
    </div>
<?php

*/



