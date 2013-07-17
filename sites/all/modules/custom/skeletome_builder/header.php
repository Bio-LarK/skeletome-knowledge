<header id="header">
    <div class="navbar navbar-inverse navbar-dark navbar-static-top" lock-to-top="model.lockToTop" bloop="model.lockToTop">

        <div class="navbar-inner">
            <div class="container-fluid">

                <div class="navbar-inner-table">

                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge">
                        <a href="{{ baseUrl }}" title="<?php print t('Home'); ?>" rel="home">
                            <img class="logo" style="height: 50px" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/skeletome_logo_RGB2.png" alt="<?php print t('Home'); ?>"/>
                        </a>

                        <?php global $user; ?>
                        <div class="btn-group show-small">
                            <?php if(isset($user->name)):?>
                                <a class="btn btn-dark-navbar" href="?q=profile-page/<?php echo $user->uid; ?>">
                                    <i class="icon-user icon-white"></i> {{ user.name | truncate:30 }}
                                </a>
                                <a class="btn btn-dark-navbar" href="{{ baseUrl }}/user/logout"><i class="ficon-signout"></i> Logout</a>
                            <?php else: ?>
                                <a class="btn btn-dark-navbar" href="{{ baseUrl }}/user/register">Register</a>

                                <a class="btn btn-dark-navbar" cm-popover cm-popover-content="{{ loginForm }}" href id="login_button"><i class="ficon-signin"></i> Log In</a>

                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="navbar-inner-table-cell">
                        <nav-search model="model.navSearchModel"></nav-search>
                    </div>

                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge navbar-inner-table-cell-browse show-large">
                        <a href="?q=taxonomy/term/{{ browseTid }}">Browse</a>
                    </div>


                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge navbar-inner-table-cell-login show-large">
                        <?php global $user; ?>
                        <div class="btn-group">
                            <?php if(isset($user->name)):?>
                                <a class="btn btn-dark-navbar" href="?q=profile-page/<?php echo $user->uid; ?>">
                                    <i class="icon-user icon-white"></i> {{ user.name | truncate:30 }}
                                </a>
                                <a class="btn btn-dark-navbar" href="{{ baseUrl }}/user/logout"><i class="ficon-signout"></i> Logout</a>
                            <?php else: ?>
                                <a class="btn btn-dark-navbar" href="{{ baseUrl }}/user/register">Register</a>

                                <a class="btn btn-dark-navbar" cm-popover cm-popover-content="{{ loginForm }}" href id="login_button"><i class="ficon-signin"></i> Log In</a>

                            <?php endif; ?>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</header>