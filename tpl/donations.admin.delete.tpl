<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.header.tpl"}
<!-- BEGIN: DONATIONS_BODY -->
<div id="main">
<table class="cells" width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td>
			<div>
				{PHP.L.donations_admin_delete_confirm}<br />
				{DONATION_ADMIN_YES} - {DONATION_ADMIN_NO}
		</td>
	</tr>
</table>
</div>
<!-- END: DONATIONS_BODY -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.footer.tpl"}
<!-- END: MAIN -->