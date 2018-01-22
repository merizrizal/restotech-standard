<?php
use yii\helpers\Html;
use restotech\standard\backend\components\Tools;

$settings_company_profile = Yii::$app->session->get('company_settings_profile'); ?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left">
                <?= Html::img(Yii::getAlias('@uploadsUrl') . Tools::thumb('/img/company/', $settings_company_profile['company_image_file'], 200, 200, true), ['class' => 'img-responsive']) ?>
            </div>
            <div class="pull-left">
                <br>
                <p style="color: #b8c7ce">
                    <?= $settings_company_profile['company_name'] ?><br><br>
                    <?= $settings_company_profile['company_address'] ?><br>
                    <?= $settings_company_profile['company_city'] . ' ' . $settings_company_profile['company_postal_code'] ?><br><br>
                    <?= $settings_company_profile['company_phone'] ?><br>
                </p>
            </div>
        </div>                    
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">  
            <li class="header">MAIN NAVIGATION</li>
            <li>
                <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'page/dashboard']); ?>">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-book"></i>
                    <span>Master Data</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">                                        
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'employee']); ?>">
                            <i class="fa fa-angle-double-right"></i>Karyawan
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'shift']); ?>">
                            <i class="fa fa-angle-double-right"></i> Shift
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'saldo-kasir']); ?>">
                            <i class="fa fa-angle-double-right"></i> Saldo Kasir
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier']); ?>">
                            <i class="fa fa-angle-double-right"></i> Supplier
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'storage']); ?>">
                            <i class="fa fa-angle-double-right"></i> Gudang
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'menu-satuan']); ?>">
                            <i class="fa fa-angle-double-right"></i> Satuan Menu
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'menu-category']); ?>">
                            <i class="fa fa-angle-double-right"></i> Kategori Menu
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'menu']); ?>">
                            <i class="fa fa-angle-double-right"></i> Menu
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'payment-method']); ?>">
                            <i class="fa fa-angle-double-right"></i> Metode Pembayaran
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'voucher']); ?>">
                            <i class="fa fa-angle-double-right"></i> Voucher
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'mtable-category']); ?>">
                            <i class="fa fa-angle-double-right"></i> Ruangan / Meja
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cubes"></i>
                    <span>Manajemen Stok</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item-category']); ?>">
                            <i class="fa fa-angle-double-right"></i> Kategori Item
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'item']); ?>">
                            <i class="fa fa-angle-double-right"></i> Item
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock-movement/index', 'type' => 'Inflow']); ?>">
                            <i class="fa fa-angle-double-right"></i> Stok Masuk
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock-movement/index', 'type' => 'Outflow']); ?>">
                            <i class="fa fa-angle-double-right"></i> Stok Keluar
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock-movement/index', 'type' => 'Transfer']); ?>">
                            <i class="fa fa-angle-double-right"></i> Stok Transfer
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock-movement/convert']); ?>">
                            <i class="fa fa-angle-double-right"></i> Stok Konversi
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock/index']); ?>">
                            <i class="fa fa-angle-double-right"></i> Koreksi Stok
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock-koreksi/index']); ?>">
                            <i class="fa fa-angle-double-right"></i> Verifikasi Koreksi Stok
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-truck"></i>
                    <span>Pembelian</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'purchase-order']); ?>">
                            <i class="fa fa-angle-double-right"></i> Purchase Order
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery']); ?>">
                            <i class="fa fa-angle-double-right"></i> Penerimaan Item PO
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery-invoice']); ?>">
                            <i class="fa fa-angle-double-right"></i> Invoice Penerimaan PO
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'retur-purchase']); ?>">
                            <i class="fa fa-angle-double-right"></i> Retur PO
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'direct-purchase']); ?>">
                            <i class="fa fa-angle-double-right"></i> Pembelian Langsung
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Penjualan</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'transaction-day/start-day']); ?>">
                            <i class="fa fa-angle-double-right"></i> Start Day
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'transaction-day/end-day']); ?>">
                            <i class="fa fa-angle-double-right"></i> End Day
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::getAlias('@rootUrl') . '/' ?>">
                            <i class="fa fa-angle-double-right"></i> Point Of Sales
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice/refund']); ?>">
                            <i class="fa fa-angle-double-right"></i> Refund
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice-payment/ar']); ?>">
                            <i class="fa fa-angle-double-right"></i> Piutang
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-money"></i>
                    <span>Transaksi Kas</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'transaction-account']); ?>">
                            <i class="fa fa-angle-double-right"></i> Account Transaksi
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'transaction-cash', 'type' => 'Cash-In']); ?>">
                            <i class="fa fa-angle-double-right"></i> Kas Masuk
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'transaction-cash', 'type' => 'Cash-Out']); ?>">
                            <i class="fa fa-angle-double-right"></i> Kas Keluar
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file-pdf-o"></i>
                    <span>Laporan</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice/report-penjualan']); ?>">
                            <i class="fa fa-angle-double-right"></i> Penjualan
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice/report-penjualan-hpp']); ?>">
                            <i class="fa fa-angle-double-right"></i> Penjualan &amp; HPP
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice/report-kas-kasir']); ?>">
                            <i class="fa fa-angle-double-right"></i> Kas Kasir
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice/report-rekap-penjualan']); ?>">
                            <i class="fa fa-angle-double-right"></i> Rekap Penjualan
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery/report-penerimaan']); ?>">
                            <i class="fa fa-angle-double-right"></i> Penerimaan Item PO
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'stock/report-stock']); ?>">
                            <i class="fa fa-angle-double-right"></i> Stok
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery-invoice/report-hutang']); ?>">
                            <i class="fa fa-angle-double-right"></i> Hutang
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'supplier-delivery-invoice-payment/report-pembayaran-hutang']); ?>">
                            <i class="fa fa-angle-double-right"></i> Pembayaran Hutang
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice-payment/report-piutang']); ?>">
                            <i class="fa fa-angle-double-right"></i> Piutang
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'sale-invoice-ar-payment/report-pembayaran-piutang']); ?>">
                            <i class="fa fa-angle-double-right"></i> Pembayaran Piutang
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'page/report-aktifitas-keuangan']); ?>">
                            <i class="fa fa-angle-double-right"></i> Aktifitas Keuangan
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Manajemen User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'user']); ?>">
                            <i class="fa fa-angle-double-right"></i>Data User
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'user-level']); ?>">
                            <i class="fa fa-angle-double-right"></i>Data User Level
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'user-app-module']); ?>">
                            <i class="fa fa-angle-double-right"></i>Application Module
                        </a>
                    </li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-wrench"></i>
                    <span>Setting</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting', 'id' => 'company']); ?>">
                            <i class="fa fa-angle-double-right"></i> Profile Perusahaan
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting', 'id' => 'tax-sc']); ?>">
                            <i class="fa fa-angle-double-right"></i> Nilai Pajak &AMP; Service Charge
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting', 'id' => 'include-tax-sc']); ?>">
                            <i class="fa fa-angle-double-right"></i> Pajak Include Service Charge
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting' , 'id' => 'transaction-day']); ?>">
                            <i class="fa fa-angle-double-right"></i> Transaction Day
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'printer']); ?>">
                            <i class="fa fa-angle-double-right"></i> Printer
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting', 'id' => 'printserver']); ?>">
                            <i class="fa fa-angle-double-right"></i> Print Server
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/update-setting', 'id' => 'struk']); ?>">
                            <i class="fa fa-angle-double-right"></i> Setting Struk
                        </a>
                    </li>
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/show-virtual-keyboard']); ?>">
                            <i class="fa fa-angle-double-right"></i> Virtual Keyboard
                        </a>
                    </li>    
                    <li>
                        <a href="<?= Yii::$app->urlManager->createUrl([Yii::$app->params['module'] . 'settings/license']); ?>">
                            <i class="fa fa-angle-double-right"></i> Lisensi
                        </a>
                    </li>
                </ul>
            </li>
        </ul>                    
    </section>
    <!-- /.sidebar -->
</aside>