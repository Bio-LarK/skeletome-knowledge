<style type="text/css">
    .section-segment-header {
        /*background-color: rgb(50, 76, 100);*/
        /*color: white;*/
        /*border-bottom: rgb(50, 76, 100);*/
    }
    .bleh-box {
        padding: 14px;;
    }
    .bleh-box .section-segment {
        padding: 14px 7px;
        border: 1px solid #eee;
        margin-bottom: 7px;;
    }
</style>
<div ng-controller="DashboardCtrl" ng-init="init()">
    <div class="row">
        <div class="span12">
            <div class="page-heading">
                <h1>Dashboard</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="span6">
            <section>
                <div class="section-segment section-segment-header">
                    <h2>Recent Pages</h2>
                </div>
                <div class="bleh-box">
                    <div ng-repeat="pageTrack in pageTracks">

                        <a href="?q=node/{{ pageTrack.field_page_tracker_node.nid }}" class="section-segment" ng-show="pageTrack.field_page_tracker_node.title">
                            <i class="icon-certificate"></i>

                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            {{ pageTrack.field_page_tracker_node.title }}
                        </a>

                        <div class="section-segment" ng-show="pageTrack.field_page_tracker_term.name">
                            <i class="icon-certificate"></i>

                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            {{ pageTrack.field_page_tracker_term.name }}
                        </div>

                        <a href="?q=search/site/{{ pageTrack.field_page_tracker_search.und[0].value }}" class="section-segment" ng-show="pageTrack.field_page_tracker_search.und[0].value">
                            <i class="icon-search"></i>

                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            Search: {{ pageTrack.field_page_tracker_search.und[0].value }}
                        </a>
                    </div>
                </div>
            </section>
        </div>
        <div class="span6">
            <section>
                <div class="section-segment section-segment-header">
                    <h2>Top 5 Pages</h2>
                </div>
                <div class="bleh-box">
                    <div ng-repeat="page in topPages">

                        <a ng-show="page.target_tid" class="section-segment" href="?q={{ page.target_tid }}">
                            <div class="pull-right">
                            <span class="label">
                                {{ page.count }}
                            </span>
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-certificate"></i>

                            {{ page.target_title }}
                        </a>

                        <a ng-show="page.target_nid" class="section-segment" href="?q={{ page.target_nid }}">
                            <div class="pull-right">
                            <span class="label">
                                {{ page.count }}
                            </span>

                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-certificate"></i>

                            {{ page.target_title }}
                        </a>

                        <a ng-show="page.target_search" class="section-segment" href="?q=search/site/{{ page.target_search }}">
                            <div class="pull-right">
                            <span class="label">
                                {{ page.count }}
                            </span>

                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-search"></i>

                            {{ page.target_search }}
                        </a>
                    </div>
                </div>

            </section>
        </div>
    </div>
    <div class="row" style="background-color: white">

        <div class="span4" ng-repeat="search in searches">
            <section>

                <div class="section-segment section-segment-header">
                    <h2><i style="position: relative; top: 9px" class="icon-search"></i> {{ search.target_search | capitalize }}</h2>
                </div>
                <div ng-show="search.isLoading">
                    <div class="refreshing-box section-segment">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>

                <div class="bleh-box">

                    <div ng-repeat="result in search.results"  >
                        <a class="section-segment" href="{{ result.link }}" ng-class="{'bleh-box-bottom': $last}">
                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>
                            {{ result.title }}
                        </a>
                    </div>
                </div>

            </section>
        </div>
    </div>

    <div class="row">
        <div class="span4">
            <section>
                <div class="section-segment section-segment-heading">
                    <h2>Recent Activity</h2>
                </div>
            </section>
        </div>

        <div class="span4">
            <section>
                <div class="section-segment section-segment-heading">
                    <h2>Monitoring</h2>
                </div>
            </section>
        </div>

        <div class="span4">

        </div>

        <div class="span4">
            <section>
                <div class="section-segment section-segment-heading">
                    <h2>Messages</h2>
                </div>
            </section>
        </div>

        <div class="span4">

        </div>

    </div>
</div>