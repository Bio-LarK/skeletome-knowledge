<section>
    <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': model.xrayState == 'isEditing' }">
        <?php if ($isAdmin || $isEditor || $isCurator): ?>
            <div class="pull-right section-segment-header-buttons">


                <div ng-switch on="model.xrayState">
                    <div ng-switch-when="isLoading">
                    </div>
                    <div ng-switch-when="isEditing">
                        <a href ng-click="cancelXRays()" class="btn btn-cancel">
                            <i class="ficon-remove"></i> Cancel
                        </a>

                        <a href ng-click="saveXRays()" class="btn btn-save">
                            <i class="ficon-ok"></i> Save
                        </a>

                    </div>
                    <div ng-switch-when="isDisplaying">
                        <a href ng-click="editXRays()" class="btn btn-edit">
                            <i class="ficon-pencil"></i> Edit
                        </a>
                    </div>
                </div>


            </div>
        <?php endif; ?>

        <h3>X-Rays</h3>
    </div>

    <div class="section-segment alert alert-success" cm-alert="model.xrayState == 'isLoading'">
        <i class="ficon-ok"></i> X-Rays Saved.
    </div>

    <div ng-switch on="model.xrayState">
        <div ng-switch-when="isLoading">
            <div class="section-segment">
                <div class="refreshing-box">
                    <i class="icon-refresh icon-refreshing"></i>
                </div>
            </div>
        </div>
        <div ng-switch-when="isEditing">
            <div class="section-segment section-segment-editing">
                <div class="dropzone" ng-model="model.edit.xrays"
                     drop-zone-upload="?q=ajax/bone-dysplasia/{{ model.boneDysplasia.nid }}/xray/add" drop-zone-message="<b>Drop X-Ray images</b> in here to upload (or click here).">
                </div>
            </div>

            <div class="section-segment section-segment-editing">
                <ul class="xray-list unstyled media-body">

                    <li class="xray-list-image-edit" ng-repeat="xray in model.edit.xrays">
                        <div ng-click="toggleXRay(xray)" style="cursor: pointer">
                            <!-- XRay images -->
                            <div class="xray-list-image-edit-image">
                                <img ng-src="{{ xray.thumb_url }}" alt=""/>
                            </div>

                            <!-- Add Button -->
                            <a class="btn btn-edit"
                               ng-class="{ 'btn-success': !xray.added, 'btn-danger': xray.added }"
                               href>
                                <i class="icon-white" ng-class="{ 'icon-plus': !xray.added, 'ficon-remove': xray.added }"></i>
                                {{ xray.added && 'Remove' || 'Re-Add' }}
                            </a>
                        </div>
                    </li>
                </ul>
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