
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
                    <h3>Inbox <span class="label label-warning">0</span></h3>
                </div>
                <div class="section-segment">
                    No messages.
                </div>
            </section>

            <section>
                <div class="section-segment section-segment-header">
                    <h3>Your Contributions</h3>
                </div>
                <div class="section-segment">
                    No contributions.
                </div>
            </section>
        </div>
        <div class="span6">
            <section>
                <div class="section-segment section-segment-header">
                    <h3>Top Pages</h3>
                </div>
                <div class="bleh-box">
                    <div ng-repeat="page in topPages">

                        <a ng-show="page.target_tid" class="section-segment" href="?q=taxonomy/term/{{ page.target_tid }}">
                            <div class="pull-right">
                            <span class="label">
                                {{ page.count }}
                            </span>
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-feature"></i>

                            {{ page.target_title }}
                        </a>

                        <a ng-show="page.target_nid" class="section-segment" href="?q=node/{{ page.target_nid }}">
                            <div class="pull-right">
                            <span class="label">
                                {{ page.count }}
                            </span>

                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-bone"></i>

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

                            <i class="ficon-search"></i>

                            {{ page.target_search }}
                        </a>
                    </div>
                </div>
            </section>

            <section>
                <div class="section-segment">
                    <h2>Past Searches</h2>
                </div>
                <div ng-repeat="search in searches">
                    <a class="section-segment" href="{{ baseUrl }}/full-search?query={{ search.target_search }}">
                        <i class="icon-search"></i>
                        <span ng-show="!search.terms.length">
                            {{ search.target_search }}
                        </span>
                        <span ng-repeat="term in search.terms">
                            <span class="label label-primary" >{{ term }}</span>
                        </span>
                    </a>
                </div>
            </section>

            <section>
                <div class="section-segment section-segment-header">
                    <h3>Recent Pages</h3>
                </div>
                <div class="bleh-box">
                    <div ng-repeat="pageTrack in pageTracks">

                        <a href="?q=node/{{ pageTrack.field_page_tracker_node.nid }}" class="section-segment" ng-show="pageTrack.field_page_tracker_node.title">

                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>

                            <i class="icon-bone"></i> {{ pageTrack.field_page_tracker_node.title }}
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


                            <div class="pull-right">
                                <i class="icon-chevron-right"></i>
                                <i class="icon-chevron-right icon-white"></i>
                            </div>


                            <i class="ficon-search"></i>

                            <span ng-show="!pageTrack.field_page_tracker_search.und[0].terms.length">
                                {{ pageTrack.field_page_tracker_search.und[0].value }}
                            </span>

                            <span ng-repeat="term in pageTrack.field_page_tracker_search.und[0].terms">
                                <span class="label label-primary" >{{ term }}</span>
                            </span>

                        </a>
                    </div>
                </div>
            </section>
        </div>
        <div class="span6">

        </div>
    </div>
    <!--<div class="row">

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
    </div>-->

    <!--<div class="row">
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
    </div>-->
</div>