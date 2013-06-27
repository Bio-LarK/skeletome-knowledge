<section>
    <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': model.xrayState == 'isEditing' }">
        <?php if ($isAdmin || $isEditor || $isCurator): ?>
            <div class="section-segment-header-buttons">


                <div ng-switch on="model.xrayState">
                    <div ng-switch-when="isLoading">
                    </div>
                    <div ng-switch-when="isEditing">
                        <save-button click="saveXRays()"></save-button>
                        <cancel-button click="cancelXRays()"></cancel-button>
                    </div>
                    <div ng-switch-when="isDisplaying">
                        <edit-button click="editXRays()"></edit-button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h3>X-Rays</h3>
    </div>

    <cm-alert state="model.xrayState" from="isLoading" to="isDisplaying">
        <i class="ficon-ok"></i> Description Saved
    </cm-alert>

    <div ng-switch on="model.xrayState">
        <div ng-switch-when="isLoading">
            <refresh-box></refresh-box>
        </div>
        <div ng-switch-when="isEditing">
            <div class="section-segment section-segment-editing">
                <div class="dropzone" ng-model="model.edit.xrays"
                     drop-zone-upload="?q=ajax/bone-dysplasia/{{ model.boneDysplasia.nid }}/xray/add" drop-zone-message="<b>Drop X-Ray images</b> in here to upload (or click here).">
                </div>
            </div>

            <div class="section-segment section-segment-editing media-body">
                <!--<ul class="xray-list unstyled media-body">-->

<!--                    <li class="xray-list-image-edit" ng-repeat="xray in model.edit.xrays">-->

                <div ng-repeat="xray in model.edit.xrays" class="xray-list-image-edit">
                    <a ng-click="removeXRay(xray)" href class="xray-list-image-edit-image">
                        <span class="btn btn-remove"><i class="ficon-remove"></i></span>
                        <img ng-src="{{ xray.thumb_url }}" alt=""/>
                    </a>
                </div>

<!--                    </li>-->
<!--                </ul>-->
            </div>


        </div>
        <div ng-switch-when="isDisplaying">
            <!-- No x-rays -->
            <div ng-show="!model.xrays.length" class="section-segment muted">
                There are no x-rays for '{{model.boneDysplasia.title}}'.
            </div>

            <!-- has x-rays -->
            <div ng-show="model.xrays.length" fancy-box="xrays" class="section-segment media-body">
                <div ng-repeat="image in model.xrays" class="xray-list-image">
                    <a class="xray-list-image-link" rel="xrays" href="{{ image.full_url }}">
                        <img ng-src="{{ image.thumb_url }}" alt=""/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>