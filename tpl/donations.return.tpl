<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.header.tpl"}
<!-- BEGIN: DONATIONS_BODY -->

		<!-- BEGIN: DONATIONS_ERROR-->
		<div class="error">
			{DONATIONS_ERROR_BODY}
		</div>
		<!-- END: DONATIONS_ERROR-->
		<div>
			{DONATIONS_RETURN_MESSAGE}
		</div>
<!-- END: DONATIONS_BODY -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.footer.tpl"}
<!-- END: MAIN -->