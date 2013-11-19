<div class="navsearch">

    <div class="navsearch-querywrapper">
        <div class="navsearch-icon media-body" >
            <i class="ficon-search" ></i>
        </div>

        <div class="navsearch-terms">
            <a href="#" ng-click="removeQueryObject(object)" ng-repeat="object in model.query" class="navsearch-term">&#215 {{ object.title || object.name }}</a>
        </div>

        <div class="navsearch-inputs">

            <input disabled
                   class="navsearch-input navsearch-selectedSuggestion"
                   ng-model="model.suggestionText"
                   type="text"
            />
            <input class="navsearch-input navsearch-query"
                   ng-change="search(model.entry)"
                   ng-change="search(model.entry)"
                   ng-model="model.entry"
                   type="text"
                   placeholder="Search for Bone Dysplasias, Clinical Features, Groups or Genes"
            />

            <span ng-show="model.suggestions.length"
              class="navsearch-helper">
                Press <span class="navsearch-helper-button">TAB</span> to complete
            </span>

            <span class="navsearch-clear" ng-click="clear()" ng-show="navSearch.query.length">&#215;</span>
        </div>
    </div>

    <ul ng-show="(model.entry.length || model.query.length) && model.isShowingSuggestions" class="navsearch-suggestions unstyled">

        <li class="navsearch-suggestion"
            ng-mouseenter="enteredSuggestion(suggestion)" ng-mouseleave="leavedSuggestion(suggestion)"
            ng-class="{'navsearch-suggestion-selected': selectedIndex == $index, 'navsearch-suggestion-round-bottom': $last}"
            ng-repeat="suggestion in model.suggestions">

            <a class="navsearch-suggestion-link" ng-show="suggestion.nid && !model.query.length" href="?q=node/{{ suggestion.nid }}">
                <span class="navsearch-suggestion-guide">
                    Go to
                </span>

                <span ng-show="suggestion.type == 'bone_dysplasia'" class="type-logo type-logo-navsearch">
                    <i class="icon-bone"></i>
                </span>
                <span ng-show="suggestion.type == 'gene'" class="type-logo type-logo-navsearch">
                    <i class="icon-gene"></i>
                </span>


                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="suggestion.title | highlight:model.entry">
                    Node
                </span>

                <!--<img ng-show="suggestion.type == 'bone_dysplasia'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-bone-dysplasia.png" alt=""/>
                <img ng-show="suggestion.type == 'gene'" class="pull-right" ng-src="{{ baseUrl }}/sites/all/modules/custom/skeletome_builder/images/logo-small-gene.png" alt=""/>-->

            </a>

            <a class="navsearch-suggestion-link" ng-show="!suggestion.nid && !model.query.length" href="?q=taxonomy/term/{{ suggestion.tid }}">
                <span class="navsearch-suggestion-guide">
                    Go to
                </span>

                <span ng-show="suggestion.machine_name == 'sk_group_tag'" class="type-logo type-logo-navsearch">
                    <i class="icon-group"></i>
                </span>

                <span ng-show="suggestion.machine_name == 'skeletome_vocabulary'" class="type-logo type-logo-navsearch">
                    <i class="icon-feature"></i>
                </span>

                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="suggestion.name | highlight:model.entry">
                    Term
                </span>


            </a>

            <a class="navsearch-suggestion-link" ng-show="model.query.length" ng-click="addToMultitermQuery(suggestion)" href>
                <span class="navsearch-suggestion-guide">
                    <i class="icon-plus" style="opacity: 0.2"></i> Add
                </span>

                <i ng-show="suggestion.type == 'bone_dysplasia'" class="icon-bone"></i>
                <i ng-show="suggestion.type == 'gene'" class="icon-gene"></i>
                <i ng-show="suggestion.machine_name == 'sk_group_tag'" class="icon-group"></i>
                <i ng-show="suggestion.machine_name == 'skeletome_vocabulary'" class="icon-feature"></i>

                <span class="navsearch-suggestion-content" ng-bind-html-unsafe="(suggestion.title || suggestion.name) | highlight:model.entry">
                    Result in here
                </span>
            </a>

        </li>

        <li class="navsearch-suggestion navsearch-suggestion-round-top"
            ng-mouseenter="enteredSuggestion()" ng-mouseleave="leavedSuggestion()"
            ng-class="{'navsearch-suggestion-selected': selectedIndex == SEARCH_SELECTED,  'navsearch-suggestion-search': model.query.length > 0, 'navsearch-suggestion-round-bottom': model.suggestions.length==0 }">

            <a class="navsearch-suggestion-link" href="{{ searchUrl() }}">
                <span class="navsearch-suggestion-guide">
                    Search for
                </span>

                <i class="ficon-search" style="position: relative; right: 4px"></i>

                <span class="navsearch-suggestion-content">
                    <span ng-repeat="term in model.query" class="navsearch-term">{{ term.title || term.name }}</span>

                    <i>{{ model.entry }}</i>...
                </span>

                <span ng-show="isLoading" class="pull-right">
                    <i class="icon-refresh icon-refreshing"></i>
                </span>

            </a>
        </li>

    </ul>
</div>