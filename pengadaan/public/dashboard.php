<!-- PANGGIL MENU.PHP -->
<?php include 'menu_public.php'; ?>

<!-- partial -->
<div class="main-panel">
  <div class="content-wrapper">

    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="d-flex justify-content-between flex-wrap">
          <div class="d-flex align-items-end flex-wrap">
            <div class="me-md-3 me-xl-5">
              <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
              <p>Status akun Anda: <span class="text-warning">Belum Diverifikasi</span></p>

              <div class="card shadow-sm">

              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body dashboard-tabs p-0">
            <ul class="nav nav-tabs px-4 border-left-0 border-top-0 border-right-0" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="sales-tab" data-bs-toggle="tab" href="#sales" role="tab" aria-controls="sales" aria-selected="false">Sales</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="purchases-tab" data-bs-toggle="tab" href="#purchases" role="tab" aria-controls="purchases" aria-selected="false">Purchases</a>
              </li>
            </ul>
            <div class="tab-content py-0 px-0 border-left-0 border-bottom-0 border-right-0">
              <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <div class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-calendar-heart"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Start date</small>
                      <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle p-0 bg-transparent border-0 text-dark shadow-none font-weight-medium" href="#" role="button" id="dropdownMenuLinkA" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <h5 class="mb-0 d-inline-block">26 Jul 2018</h5>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkA">
                          <a class="dropdown-item" href="#">12 Aug 2018</a>
                          <a class="dropdown-item" href="#">22 Sep 2018</a>
                          <a class="dropdown-item" href="#">21 Oct 2018</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Revenue</small>
                      <h5 class="me-2 mb-0">$577545</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Total views</small>
                      <h5 class="me-2 mb-0">9833550</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Downloads</small>
                      <h5 class="me-2 mb-0">2233783</h5>
                    </div>
                  </div>
                  <div class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Flagged</small>
                      <h5 class="me-2 mb-0">3497843</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <div class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-calendar-heart"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Start date</small>
                      <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle p-0 bg-transparent border-0 text-dark shadow-none font-weight-medium" href="#" role="button" id="dropdownMenuLinkB" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <h5 class="mb-0 d-inline-block">26 Jul 2018</h5>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkB">
                          <a class="dropdown-item" href="#">12 Aug 2018</a>
                          <a class="dropdown-item" href="#">22 Sep 2018</a>
                          <a class="dropdown-item" href="#">21 Oct 2018</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Revenue</small>
                      <h5 class="me-2 mb-0">$577545</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Total views</small>
                      <h5 class="me-2 mb-0">9833550</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Downloads</small>
                      <h5 class="me-2 mb-0">2233783</h5>
                    </div>
                  </div>
                  <div class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Flagged</small>
                      <h5 class="me-2 mb-0">3497843</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                <div class="d-flex flex-wrap justify-content-xl-between">
                  <div class="d-none d-xl-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-calendar-heart"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Start date</small>
                      <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle p-0 bg-transparent border-0 text-dark shadow-none font-weight-medium" href="#" role="button" id="dropdownMenuLinkC" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <h5 class="mb-0 d-inline-block">26 Jul 2018</h5>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLinkC">
                          <a class="dropdown-item" href="#">12 Aug 2018</a>
                          <a class="dropdown-item" href="#">22 Sep 2018</a>
                          <a class="dropdown-item" href="#">21 Oct 2018</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-currency-usd"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Revenue</small>
                      <h5 class="me-2 mb-0">$577545</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-eye"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Total views</small>
                      <h5 class="me-2 mb-0">9833550</h5>
                    </div>
                  </div>
                  <div class="d-flex border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-download"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Downloads</small>
                      <h5 class="me-2 mb-0">2233783</h5>
                    </div>
                  </div>
                  <div class="d-flex py-3 border-md-right flex-grow-1 align-items-center justify-content-left justify-content-md-center px-4 px-md-0 mx-1 mx-md-0 p-3 item">
                    <div class="icon-box-secondary me-3">
                      <i class="mdi mdi-flag"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                      <small class="mb-1 text-muted">Flagged</small>
                      <h5 class="me-2 mb-0">3497843</h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PANGGIL footer.PHP -->
    <?php include 'footer.php'; ?>