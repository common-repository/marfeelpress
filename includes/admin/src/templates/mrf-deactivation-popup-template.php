<?php

use Admin\Marfeel_Press_Admin_Translator;
use Ioc\Marfeel_Press_App;

$deactivate_logo_src = MRFP__MARFEEL_PRESS_ADMIN_RESOURCES_DIR . 'images/deactivate.svg';
$insight_api_token   = Marfeel_Press_App::make( 'settings_service' )->get( 'marfeel_press.insight_token' );
$api_url             = MRFP_INSIGHT_API . '/tenants/' . Marfeel_Press_App::make( 'definition_service' )->get( 'tenant_home' ) . '/definitions/index';
$slug                = 'marfeelpress';

$reasons = array(
	Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.doesnt_display' ),
	Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.dont_see_ads' ),
	Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.more_money' ),
	Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.menu_problem' ),
	Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.others' ),
);


?>

<div class="mrf-wp">
	<div class="modal fade hide deactivation-popup" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title font-weight-bold"><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.title' ); ?></h5>
					<div class="icon-container">
						<span class="icon-close-esc icon"></span>
					</div>
				</div>
				<div class="modal-body">
					<div class="mrf-wizard-steps__body">
						<div class="split-view-step row">
							<div class="split-view-step__left text-center px-5">
								<div class="text-center"><img src="<?php echo $deactivate_logo_src; ?>" alt="Deactivation logo">
									<h4 class="font-weight-bold mt-4"><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.help_improving' ); ?></h4>
									<p><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.fill_information' ); ?></p>
								</div>
							</div>
							<div class="split-view-step__right col px-5">
								<div class="row">
									<fieldset class="col">
										<legend class="font-weight-bold">
											<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason' ); ?>
										</legend>

										<?php foreach ( $reasons as $key => $reason ) { ?>
											<div class="custom-control custom-checkbox">
												<input id="reason-<?php echo $key; ?>"
                                                   name="reason" value="<?php echo $reason; ?>" type="checkbox" class="custom-control-input">
												<label for="reason-<?php echo $key; ?>" class="custom-control-label"><?php echo $reason; ?></label>
											</div>
										<?php } ?>
									</fieldset>
								</div>
								<div class="row mt-4">
									<fieldset class="col">
										<legend class="font-weight-bold">
											<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.additional_comments' ); ?>
										</legend>
										<label for="additionalComments" class="invisible position-absolute">
											<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.additional_comments_placeholder' ); ?>
										</label>
										<textarea id="additionalComments"
										          placeholder="<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.additional_comments_placeholder' ); ?>"
										          name="additionalComments" class="form-control" rows="5"></textarea>
                                        <div class="alert-danger validation-error additional-comments-error-message hidden">
	                                        <?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.additional_comments_required' ); ?>
                                        </div>
									</fieldset>
								</div>
							</div>
						</div>
					</div>
				</div>
				<footer class="modal-footer">
					<button class="mrf-wizard-steps__footer__back btn btn-secondary mr-auto">
						<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.back' ); ?>
					</button>
					<button type="submit" class="btn btn-async btn-danger initial">
						<div class="btn-async__initial">
							<span class="copy"><?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.deactivate' ); ?></span>
						</div>
					</button>
				</footer>
			</div>
		</div>
	</div>
</div>

<script>
    (function () {
        const deactivationLink = document.querySelector('#the-list .active[data-slug=<?php echo $slug; ?>] .deactivate > a');
        const deactivationPopup = document.querySelector('.modal.deactivation-popup');
        const additionalCommentsField = deactivationPopup.querySelector('#additionalComments');
        const additionalCommentsErrorMessage = deactivationPopup.querySelector('.additional-comments-error-message');
        const MODAL_ANIMATION_TIME_MS = 300;
        const ESCAPE_KEY_CODE = 27;
        const SHOW = 'show';
        const HIDE = 'hide';
        const D_BLOCK = 'd-block';
        const HIDDEN = 'hidden';
        const OTHER_REASON_ID = '#reason-4';

        function closeModal() {
            deactivationPopup.classList.remove(SHOW);
            deactivationPopup.classList.add(HIDE);
            setTimeout(function () {
                deactivationPopup.classList.remove(D_BLOCK);
            }, MODAL_ANIMATION_TIME_MS);
        }

        function showModal() {
            deactivationPopup.classList.remove(HIDE);
            deactivationPopup.classList.add(D_BLOCK);
            setTimeout(function () {
                deactivationPopup.classList.add(SHOW);
            }, MODAL_ANIMATION_TIME_MS);
        }

        function addEventListeners() {
            addCloseModalListeners();
            addModalDeactivateButtonListener();
            addValidationListener();
        }

        function addCloseIconListener() {
            deactivationPopup.querySelector('.icon-container').addEventListener('click', function () {
                closeModal();
            });
        }

        function addEscapeListener() {
            document.onkeydown = function (evt) {
                evt = evt || window.event;
                if (evt.keyCode == ESCAPE_KEY_CODE) {
                    closeModal();
                }
            };
        }

        function addBackButtonListener() {
            deactivationPopup.querySelector('.modal-footer .mrf-wizard-steps__footer__back.btn').addEventListener('click', function () {
                closeModal();
            });
        }

        function addClickOutsideListener() {
            deactivationPopup.addEventListener('click', function () {
                closeModal();
            });
            deactivationPopup.querySelector('.modal-dialog').addEventListener('click', function (event){
                event.stopPropagation();
            });
        }

        function addCloseModalListeners() {
            addCloseIconListener();
            addEscapeListener();
            addBackButtonListener();
            addClickOutsideListener();
        }

        function addModalDeactivateButtonListener() {
            deactivationPopup.querySelector('.modal-footer button[type=submit]').addEventListener('click', function () {
                const selectedReasons = getSelectedReasons();
                const additionalComments = additionalCommentsField.value;

                if(!validForm()) {
                    showValidationErrors();
                    additionalCommentsField.focus();
                    return;
                }

                if (selectedReasons.length > 0 || !!additionalComments) {
                    var feedback = prepareFeedback(selectedReasons, additionalComments);
                    sendFeedback(feedback).finally(function () {
                        closeModal();
                        deactivatePlugin(feedback);
                    });
                } else {
                    deactivatePlugin();
                    closeModal();
                }
            });
        }

        function getSelectedReasons() {
            return Array.from(deactivationPopup.querySelectorAll('input[type=checkbox][name=reason]:checked'))
                .map(function (reason) {
                    return reason.value;
                });
        }

        function deactivatePlugin(feedback) {
            window.location.href = deactivationLink.href + '&message=' + feedback;
        }

        function sendFeedback(feedback) {
            return fetch('<?php echo $api_url; ?>/deactivation/email?message=' + feedback, {
                method: 'post',
                headers: {
                    'mrf-secret-key': '<?php echo $insight_api_token; ?>',
                    'Content-Type': 'application/json'
                }
            })
        }

        function validForm() {
            return validateAdditionalComments();
        }

        function validateAdditionalComments() {
            return !(getSelectedReasons().includes('<?php echo Marfeel_Press_Admin_Translator::trans( 'mrf.deactivation_popup.reason.others' ); ?>')
                && !additionalCommentsField.value);
        }

        function addValidationListener() {
            addOtherCheckboxListener();
        }

        function addValidationCommentsListener() {
            additionalCommentsField.addEventListener('keyup', validationCommentsHandler);
        }

        function removeValidationCommentsListener() {
            additionalCommentsField.removeEventListener('keyup', validationCommentsHandler);
        }

        function validationCommentsHandler() {
            if(validForm()) {
                hideValidationErrors();
            } else {
                showValidationErrors();
            }
        }

        function addOtherCheckboxListener() {
            deactivationPopup.querySelector(OTHER_REASON_ID).addEventListener('change', function(event) {
                if(event.target.checked) {
                    addValidationCommentsListener();
                } else {
                    removeValidationCommentsListener();
                    hideValidationErrors();
                }
            });
        }

        function showValidationErrors() {
            additionalCommentsErrorMessage.classList.remove(HIDDEN);
        }

        function hideValidationErrors() {
            additionalCommentsErrorMessage.classList.add(HIDDEN);
        }

        function prepareFeedback(reasons, comments) {
            if (!!comments) {
                reasons.push('Comments:' + comments);
            }
            return reasons.join('|');
        }

        deactivationLink.addEventListener('click', function (event) {
            event.preventDefault();
            addEventListeners();
            showModal();
        });
    })();
</script>
