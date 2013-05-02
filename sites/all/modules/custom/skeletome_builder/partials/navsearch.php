<div class="navsearch">
    <div class="navsearch-inputs">


        <input disabled class="navsearch-input navsearch-selectedSuggestion" ng-model="navSearch.selectedSuggestion" type="text"/>
        <input class="navsearch-input navsearch-query"
               ng-model="navSearch.query"
               type="text"
               ng-change="search(navSearch.query)"
               placeholder="Search for Bone Dysplasias, Groups or Genes"/>

        <i class="icon-search navsearch-icon"></i>

        <span ng-show="navSearch.selectedSuggestion.length && navSearch.selectedSuggestion != navSearch.query && !isMultitermQuery()"
            class="navsearch-helper">
            Press TAB to add another term
        </span>
    </div>

    <!--<a class="btn btn-success navsearch-searchbar-button" href="?q=search/site/{{ navSearch.query }}">
        <i class="icon-search icon-white"></i> Search
    </a>-->


    <ul ng-show="navSearch.query.length && showSuggestions" class="navsearch-suggestions unstyled">
        <li class="navsearch-suggestion"
            ng-mouseenter="enteredSuggestion()" ng-mouseleave="leavedSuggestion()"
            ng-class="{'navsearch-suggestion-selected': selectedIndex == SEARCH_SELECTED}">
            <a class="navsearch-suggestion-link" href="?q=search/site/{{ navSearch.query }}*">
                <span class="navsearch-suggestion-guide">
                    Search for
                </span>
                <span class="navsearch-suggestion-content">
                    <b>{{ navSearch.query }}</b>...
                </span>
                <i class="icon-search pull-right" style="position: relative; right: 5px;top:2px;"></i>
            </a>
        </li>
        <li class="navsearch-suggestion"
            ng-mouseenter="enteredSuggestion(suggestion)" ng-mouseleave="leavedSuggestion(suggestion)"
            ng-class="{'navsearch-suggestion-selected': selectedIndex == $index}"
            ng-repeat="suggestion in navSearch.querySuggestions">

            <a class="navsearch-suggestion-link" ng-show="suggestion.nid && !isMultitermQuery()" href="?q=node/{{ suggestion.nid }}">
                <span class="navsearch-suggestion-guide">
                    Go to
                </span>
                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="suggestion.title | highlight:navSearch.query">
                    Node result in here
                </span>

                <img ng-show="suggestion.type == 'bone_dysplasia'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia.png" alt=""/>
                <img ng-show="suggestion.type == 'gene'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-gene.png" alt=""/>

            </a>

            <a class="navsearch-suggestion-link" ng-show="!suggestion.nid && !isMultitermQuery()" href="?q=taxonomy/term/{{ suggestion.tid }}">
                <span class="navsearch-suggestion-guide">
                    Go to
                </span>
                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="suggestion.name | highlight:navSearch.query">
                    Term result in here
                </span>

                <img ng-show="suggestion.machine_name == 'sk_group_tag'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia-group.png" alt=""/>

                <img ng-show="suggestion.machine_name == 'skeletome_vocabulary'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-phenotype.png" alt=""/>

            </a>

            <a class="navsearch-suggestion-link" ng-show="isMultitermQuery()" ng-click="addToMultitermQuery(suggestion.title || suggestion.name)" href>
                <span class="navsearch-suggestion-guide">
                    <i class="icon-plus" style="opacity: 0.2"></i> Add
                </span>
                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="(suggestion.title || suggestion.name) | highlight:navSearch.query">
                    Result in here
                </span>

                <img ng-show="suggestion.type == 'bone_dysplasia'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia.png" alt=""/>
                <img ng-show="suggestion.type == 'gene'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-gene.png" alt=""/>
                <img ng-show="suggestion.machine_name == 'sk_group_tag'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia-group.png" alt=""/>
                <img ng-show="suggestion.machine_name == 'skeletome_vocabulary'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-phenotype.png" alt=""/>
            </a>


        </li>
    </ul>
</div>