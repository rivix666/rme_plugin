<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $this->has_header_logo() ) {
			$this->header_logo();
		} else {
			echo $this->get_title();
		}
		?>
		</td>
		<td class="shop-info">
			<div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
			<div class="shop-address"><?php $this->shop_address(); ?></div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
<?php if( $this->has_header_logo() ) echo $this->get_title(); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->type, $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
			<?php do_action( 'wpo_wcpdf_before_billing_address', $this->type, $this->order ); ?>
			<?php $this->billing_address(); ?>
			<?php do_action( 'wpo_wcpdf_after_billing_address', $this->type, $this->order ); ?>
			<?php if ( isset($this->settings['display_email']) ) { ?>
			<div class="billing-email"><?php $this->billing_email(); ?></div>
			<?php } ?>
			<?php if ( isset($this->settings['display_phone']) ) { ?>
			<div class="billing-phone"><?php $this->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address shipping-address">
			<?php if ( isset($this->settings['display_shipping_address']) && $this->ships_to_different_address()) { ?>
			<h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
			<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->type, $this->order ); ?>
			<?php $this->shipping_address(); ?>
			<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->type, $this->order ); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $this->type, $this->order ); ?>
				<?php if ( isset($this->settings['display_number']) ) { ?>
				<tr class="invoice-number">
					<th><?php _e( 'Invoice Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->invoice_number(); ?></td>
				</tr>
				<?php } ?>
				<?php if ( isset($this->settings['display_date']) ) { ?>
				<tr class="invoice-date">
					<th><?php _e( 'Invoice Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->invoice_date(); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->order_date(); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $this->payment_method(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $this->type, $this->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e('Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e('Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e('Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $items = $this->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
			<td class="product">
				<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
				<span class="item-name"><?php echo $item['name']; ?></span>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
				<span class="item-meta"><?php echo $item['meta']; ?></span>
				<dl class="meta">
					<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<?php if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
					<?php if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
				</dl>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
			</td>
			<td class="quantity"><?php echo $item['quantity']; ?></td>
			<td class="price"><?php echo $item['order_price']; ?></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->type, $this->order ); ?>
					<?php if ( $this->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->type, $this->order ); ?>
				</div>				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php foreach( $this->get_woocommerce_totals() as $key => $total ) : ?>
						<tr class="<?php echo $key; ?>">
							<td class="no-borders"></td>
							<th class="description"><?php echo $total['label']; ?></th>
							<td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
						</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>

<?php if ( $this->get_footer() ): ?>
<div id="footer">
	<?php $this->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>


<!-- RME includes -->
<?php include_once "rme_funcs.php"; ?>

<!-- Agreement -->
<div style="page-break-before: always;"></div>

<div class="agreement">
	<div class="agreement intro">
		<h1 class="document-type-label">UMOWA</h1>
		<p><b>Ważna od <?php introStartDate($this->order); ?> do <?php introExpDate($this->order); ?> </b></p>
		<p>Niniejszą umowę zawarto w dniu <?php introStartDate($this->order); ?> w <?php introCity($this->order); ?> pomiędzy:</p>
		<p>Impuls System Sp. z o.o. z siedzibą w Opolu (45-056) przy ul. Plac Teatralny 13, zarejestrowaną w Sądzie Rejonowym w Opolu, wydział KRS pod numerem KRS 773093, NIP 7543210455, REGON 382621111 reprezentowaną przez – Tomasz Ordaszewski – Prezes Zarządu zwanym dalej <b>Impuls System</b></p>
		<p>a</p>
		<p><?php introClientData($this->order); ?></p>
	</div>
	<div class="agreement content">
		<ul style="list-style-type:none;">
  			<li>
				<b>§1</b>
				<ol>
					<li>Na zasadach określonych niniejszą umową Impuls System zobowiązuje się do udostępnienia na rzecz Klienta poprzez Internet materiału dźwiękowego, w tym utworów muzycznych zwanych dalej Radio Max Elektro , w celu publicznego odtwarzania, a Klient zobowiązuje się do zgodnego z umową korzystania z udostępnionego przez Impuls System materiału dźwiękowego wyłącznie w placówkach prowadzonej przez siebie działalności oraz do zapłaty wynagrodzenia na rzecz Impuls System.</li>
					<li>
						Lista placówek objętych umową (adres miejsca w którym będzie grane Radio Max Elektro) :
						<ol>
							<li> <?php contentLicenceAddress($this->order); ?> </li>
						</ol>
					</li>
				</ol>
			</li>
			<li>
				<b>§2</b>
				<ol>
					<li>Impuls System oświadcza, iż jest właścicielem wszelkich praw do materiałów dźwiękowych, które będą udostępnianie Klientowi w ramach realizacji niniejszej umowy, w tym do utworów muzycznych, serwisów informacyjnych, audycji tematycznych nadawanych przez Internet pod nazwą Radio Impuls.</li>
					<li>Prawa do materiałów muzycznych określone w ustępie 1 - niniejszego paragrafu, chronione są prawem autorskim, na zasadach określonych w ustawie z 4 lutego 1994 r. o prawie autorskimi prawach pokrewnych (j.t. Dz. U. z 2000 r., nr 80, poz. 904 ze zm.) zwanej dalej Ustawą o prawie autorskim i prawach pokrewnych.</li>
					<li>Na zasadach i w granicach określonych umową, Impuls System udziela Klientowi licencji wyłącznie na korzystanie z opisanych powyżej materiału dźwiękowego. Licencja wygasa wraz z rozwiązaniem umowy.</li>
				</ol>
			</li>
			<li>
				<b>§3</b>
				<ol>
					<li>Impuls System przekaże Klientowi nie później niż 24 godziny po realizacji zamówienia  link z adresem https:// do serwera Radia MaxElektro. Klient może otrzymać kod dostępu niezbędny do autoryzacji. Kod dostępu jest tajny a Klient nie ma prawa udostępniać go osobom trzecim.</li>
					<li>Klient będzie używał własnego sprzętu komputerowego oraz oprogramowania do odbioru przez Internet oraz do odtwarzania Radia MaxElektro udostępnianego przez Impuls System.</li>
				</ol>
			</li>
			<li>
				<b>§4</b>
				<ol>					
					<li>Za udostępnienie przez Impuls System materiału dźwiękowego zwanego Radio Impuls na zasadach określonych w umowie, Klient zapłaci na rzecz Impuls System miesięczny abonament w kwocie 39 zł ( słownie: trzydzieści dziewięć złotych) netto za każdą placówkę objętą umową.</li>
					<li>
						Adekwatnie do czasu trwania umowy klient wybiera jedną z poniższych opcji płatności:
						<ol>
							<li><b>PLAN 6</b>: opłata abonamentowa pobierana jednorazowo za sześć miesięcy z góry w kwocie 234 zł ( słownie: dwieście trzydzieści cztery złote)</li>
							<li><b>PLAN 12</b>: opłata abonamentowa pobierana jednorazowo za dwanaście miesięcy z góry w kwocie 444,6 zł ( słownie: czterysta czterdzieści cztery złote, sześćdziesiąt groszy) w tym <b>5 % rabatu</b></li>
							<li><b>PLAN 24</b>: opłata abonamentowa pobierana jednorazowo za dwadzieścia cztery miesiące z góry w kwocie 842,4 zł ( słownie: osiemset czterdzieści dwa złote, czterdzieści groszy) w tym <b>10 % rabatu</b></li>
						</ol>
					</li>
					<li>Do podanych kwot należy doliczyć podatek VAT w wysokości 23%.</li>
					<li>Klient nie jest uprawniony do rejestrowania materiału dźwiękowego udostępnianego przez Impuls System. Klient może wykorzystywać materiał dźwiękowy tylko na zasadach określonych w umowie, jednocześnie nie może ich udostępniać do korzystania innym podmiotom.</li>
				</ol>
			</li>
			<li>
				<b>§5</b>
				<ol>					
					<li>Uznaje się, iż Impuls System udostępnia materiał dźwiękowy, jeżeli następuje transmisja tego materiału wychodząca z urządzeń Impuls System.</li>
					<li>Klient we własnym zakresie zapewnia wszelkie urządzenia niezbędne do emisji materiału dźwiękowego w prowadzonej przez niego placówce.</li>
					<li>Źródła emisji materiału dźwiękowego nie mogą być ulokowane poza placówką Klienta.</li>
					<li> Impuls System nie ponosi odpowiedzialności za brak transmisji z powodu przerw w dostawie energii elektrycznej, w ruchu w sieci Internet, w przypadku niewłaściwego działania zestawu komputerowego lub w razie przyczyn wywołanych siłą wyższą.</li>
				</ol>
			</li>
			<li>
				<b>§6</b>
				<ol>					
					<li>Klient oświadcza, iż jego placówki mają zapewniony stały dostęp do sieci Internet.</li>
					<li>Przez zawarcie umowy strony oświadczają, iż wymogi techniczne posiadanych przez nie urządzeń, pozwalają na realizację umowy.</li>
				</ol>
			</li>
			<li>
				<b>§7</b>
				<ol>					
					<li>Płatności określone w niniejszej umowie dokonywane są na rzecz Impuls System na rachunek bankowy : ING Bank Śląski S.A.Nr 03 1050 1504 1000 0090 8060 4094.</li>
					<li>Za dzień dokonania zapłaty, uważa się dzień uznania tego rachunku. 3.Kwoty abonamentu mogą zostać waloryzowane o wartość wynikającą ze wskaźnika wzrostu cen towarów i usług ogłoszonego przez Prezesa GUS za rok poprzedni. Waloryzacja następuje począwszy od miesiąca, w którym wskaźnik ten został ogłoszony. O zaistniałej waloryzacji Impuls System zawiadamia Klienta na piśmie oraz przez Internet.</li>
				</ol>
			</li>
			<li>
				<b>§8</b>
				<ol>					
					<li>Niniejsza umowa zawarta jest na okres <?php contentLicenceType($this->order); ?>.</li>
					<li>Każda ze stron może rozwiązać niniejszą umowę z zachowaniem miesięcznego okresu wypowiedzenia.</li>
					<li>Impuls System jest uprawniony do rozwiązania umowy bez wypowiedzenia, w razie opóźnienia się przez Klienta z zapłatą należności o co najmniej 14 dni, a także w razie ustalenia, iż Klient wykorzystuje materiał dźwiękowy niezgodnie z umową.</li>
					<li>Klient jest uprawniony do rozwiązania umowy bez wypowiedzenia, w razie przerwy w udostępnianiu materiału dźwiękowego spowodowanej przyczynami leżącymi po stronie Impuls System, trwającej w roku kalendarzowym w sposób ciągły przez 10 dni.</li>
				</ol>
			</li>
			<li>
				<b>§9</b>
				<ol>					
					<li>Zapłacony na rzecz Impuls System abonament i inne kwoty nie podlegają zwrotowi.</li>
				</ol>
			</li>
			<li>
				<b>§10</b>
				<ol>					
					<li>Rozwiązanie lub odstąpienie od umowy powoduje natychmiastową utratę prawa do korzystania przez Klienta z Radia Max Elektro.</li>
				</ol>
			</li>
			<li>
				<b>§11</b>
				<ol>					
					<li>Umowę sporządzono w dwóch jednobrzmiących egzemplarzach, po jednym dla każdej ze stron.</li>
				</ol>
			</li>
		</ul> 
	</div>
</div>