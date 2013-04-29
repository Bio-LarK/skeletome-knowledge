<div class="cf-selector">



    <div>
        <div class="section-inner section-inner-heading">
            <h1>Clinical Features</h1>
            <search model="query" change="" placeholder="Search for a clinical feature"></search>
        </div>

        <div ng-show="query.length" >
            <div class="section-inner section-inner-shadow">
                Results
            </div>
            <ul>
                <li>Result</li>
            </ul>
        </div>

        <div ng-show="!query.length" class="cf-selector-columnholder">
            <div class="cf-selector-columnwrapper">

                <div class="cf-selector-column">
                    <div class="section-inner section-inner-heading section-inner-shadow">
                        <div ng-show="previousClinicalFeature.parentClinicalFeature">
                            <a ng-click="selectPreviousClinicalFeature(clinicalFeature)" class="btn"><i class="icon-arrow-left"></i> Back to {{ previousClinicalFeature.parentClinicalFeature.name }}</a>
                        </div>
                        <h3>{{ previousClinicalFeature.name }}</h3>
                    </div>

                    <ul class="unstyled">
                        <li ng-repeat="clinicalFeature in previousClinicalFeature.childrenClinicalFeatures" >
                            <a ng-click="selectNextClinicalFeature(clinicalFeature)" href class="section-inner">
                                <i class="icon-chevron-right pull-right"></i>
                                {{ clinicalFeature.name }}
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="cf-selector-column">
                    <div class="section-inner section-inner-heading section-inner-shadow">
                        <div ng-click="selectPreviousClinicalFeature(currentClinicalFeature.parentClinicalFeature)" >
                            <a ng-show="currentClinicalFeature.parentClinicalFeature" class="btn"><i class="icon-arrow-left"></i> Back to {{ currentClinicalFeature.parentClinicalFeature.name }}</a>
                        </div>
                        <h3>{{ currentClinicalFeature.name }}</h3>
                    </div>

                    <ul class="unstyled">
                        <li ng-repeat="clinicalFeature in currentClinicalFeature.childrenClinicalFeatures" >
                            <a ng-click="selectNextClinicalFeature(clinicalFeature)" href class="section-inner">

                                <i ng-show="!clinicalFeature.isLoading" class="icon-chevron-right pull-right"></i>
                                <i ng-show="clinicalFeature.isLoading" class="icon-refresh icon-refreshing pull-right"></i>

                                {{ clinicalFeature.name }}
                            </a>
                        </li>
                    </ul>
                </div>


                <div class="cf-selector-column">
                    <div class="section-inner section-inner-heading section-inner-shadow">
                        <div>
                            <a class="btn"><i class="icon-arrow-left"></i> Back to {{ nextClinicalFeature.parentClinicalFeature.name }}</a>
                        </div>
                        <h3>{{ nextClinicalFeature.name }}</h3>
                    </div>
                    <ul class="unstyled">
                        <li ng-repeat="clinicalFeature in nextClinicalFeature.childrenClinicalFeatures" >
                            <a href class="section-inner">
                                <i class="icon-chevron-right pull-right"></i>
                                {{ clinicalFeature.name }}
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <style type="text/css">

        .cf-selector {
            width: 500px;
            /*padding: 20px;*/
            margin: 0 auto;
            border: 1px solid #bbb;
            background-color: white;
        }

        .section-inner-heading {
            padding: 20px;
        }
        .section-inner-shadow {
            -webkit-box-shadow: 0 5px 9px -6px rgba(0, 0, 0, 0.3) inset;
        }
        .section-inner {
            padding: 14px;
            background-color: white;
            border-bottom: 1px solid #ddd;
            margin-bottom: 0px;
        }

        .cf-selector-columnholder {
            overflow: hidden;
        }
        .cf-selector-columnwrapper {
            width: 1560px;
            position: relative;
            left: -520px;
            overflow: auto;
        }
        .cf-selector-column {
            float: left;
            width: 500px;
            margin-right: 20px;;
        }
        .cf-selector-column li  {
            margin-bottom: 0px;
        }
        .cf-selector-column li a {
            display: block;
        }
    </style>

</div>

