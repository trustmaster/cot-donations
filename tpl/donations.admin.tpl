<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.header.tpl"}
<!-- BEGIN: DONATIONS_BODY -->
<div id="main">
<table class="cells" width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td>
			<!-- BEGIN: DONATIONS_PENDING -->
			<h2>{PHP.L.donations_admin_pending}</h2>
			<table class="cells">
				<tr>
					<td class="coltop"></td>
					<td class="coltop">{PHP.L.User}</td>
					<td class="coltop">{PHP.L.Name}</td>
					<td class="coltop">{PHP.L.donations_admin_amount}</td>
					<td class="coltop">{PHP.L.Email}</td>
					<td class="coltop">{PHP.L.Date}</td>
					<td class="coltop">{PHP.L.donations_admin_txnid}</td>
				</tr>
				<!-- BEGIN: LOOP -->
				<tr>
					<td>{DONATION_ADMIN_EDIT} - {DONATION_ADMIN_DELETE}</td>
					<td>{DONATION_ADMIN_USER}</td>
					<td>{DONATION_ADMIN_NAME}</td>
					<td>{DONATION_ADMIN_AMOUNT}</td>
					<td>{DONATION_ADMIN_EMAIL}</td>
					<td>{DONATION_ADMIN_DATE}</td>
					<td>{DONATION_ADMIN_TXNID}</td>
				</tr>
				<!-- END: LOOP -->
				<!-- BEGIN: EMPTY -->
				<tr>
					<td colspan="7">{PHP.L.donations_admin_pending_empty}</td>
				</tr>
				<!-- END: EMPTY -->
			</table>
			<!-- END: DONATIONS_PENDING -->
			<br />
			<hr>
			<br />
			<!-- BEGIN: DONATIONS_COMPLETED -->
			<h2>{PHP.L.donations_admin_completed}</h2>
			<div style="padding-bottom:5px;float:right;">{DONATIONS_COMPLETED_PAGINATION}</div>
			<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.User}</td>
					<td class="coltop">{PHP.L.Name}</td>
					<td class="coltop">{PHP.L.donations_admin_amount}</td>
					<td class="coltop">{PHP.L.Email}</td>
					<td class="coltop">{PHP.L.Date}</td>
					<td class="coltop">{PHP.L.donations_admin_txnid}</td>
				</tr>
				<!-- BEGIN: LOOP -->
				<tr>
					<td>{DONATION_ADMIN_USER}</td>
					<td>{DONATION_ADMIN_NAME}</td>
					<td>{DONATION_ADMIN_AMOUNT}</td>
					<td>{DONATION_ADMIN_EMAIL}</td>
					<td>{DONATION_ADMIN_DATE}</td>
					<td>{DONATION_ADMIN_TXNID}</td>
				</tr>
				<!-- END: LOOP -->
				<!-- BEGIN: EMPTY -->
				<tr>
					<td colspan="6">{PHP.L.donations_admin_completed_empty}</td>
				</tr>
				<!-- END: EMPTY -->
			</table>
			<div style="padding-top:5px;float:right;">{DONATIONS_COMPLETED_PAGINATION}</div>
			<!-- END: DONATIONS_COMPLETED -->
		</td>
	</tr>
</table>
</div>
<!-- END: DONATIONS_BODY -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.footer.tpl"}
<!-- END: MAIN -->