<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Assistance Page Guide</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- For the toast messages -->
    <link href="../css/toast.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- For fontawesome -->
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>

    <!-- For table sorting -->
    <link rel="stylesheet" href="../Table Sorting/tablesort.css">

    <!-- For table sorting -->
    <link rel="stylesheet" href="../Table Sorting/tablesort.css">

    <!-- For Modal -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css"
        integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <style>
        .helpContents {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: white;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        @keyframes bubbleAnimation {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .bubble {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            animation: bubbleAnimation 1s ease-out;
        }

        .text-black {
            color: black;
            font-weight: 350;
        }
    </style>
    <!-- Bubble animation -->
    <script>
        function showBubbleAnimation(event) {
            const navLink = event.currentTarget;
            const rect = navLink.getBoundingClientRect();
            const bubble = document.createElement('span');
            bubble.classList.add('bubble');
            bubble.style.top = `${event.clientY - rect.top}px`;
            bubble.style.left = `${event.clientX - rect.left}px`;
            navLink.appendChild(bubble);
            setTimeout(() => {
                bubble.remove();
            }, 1000);
        }
    </script>


</head>

<body id="page-top">
    <!-- Font Awesome -->
    <script src="js/scripts.js"></script>
    <!-- Page Wrapper -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <div class="justify-content-center">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow"
                    style="background-color: rgb(28,35,47);">

                    <!-- Sidebar Toggle (Topbar) -->

                    <!-- Sidebar - Brand -->
                    <a class="navbar-brand d-flex align-items-center justify-content-center" href="index.php"
                        style="background-color: rgb(28,35,47);">
                        <div class="fa-regular fa-hand" style="color:white"> Online Guide</div>
                        <a href="../Assistance Card Page/assistanceCard.php" class="btn btn-sm btn-outline-light ml-2"
                            onclick="showSnackbar('redirect to online guide'); showBubbleAnimation(event);">Back to
                            Dashboard</a>
                        <a href="./online_Help.php" class="btn btn-sm btn-outline-light ml-2"
                            onclick="showSnackbar('redirect to online guide'); showBubbleAnimation(event);">Back to
                            Guide Home Page</a>
                    </a>

                    <!-- Nav Item - Glove Guide -->
                    <ul class="navbar-nav ml-auto d-flex align-items-center justify-content-center">
                        <li class="nav-item active">
                            <a onclick="showSnackbar('redirect to assistance page'); showBubbleAnimation(event);"
                                class="nav-link" href="./glove_Guide.php">
                                <i class="fa-solid fa-hand"></i>
                                <span class="ml-1">Glove Guide</span>
                            </a>
                        </li>

                        <!-- Nav Item - Login Guide -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);"
                                class="nav-link" href="./loginGuide.php">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span class="ml-1">Login</span>
                            </a>
                        </li>

                        <!-- Nav Item - Assistance Page -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to assistance page guide'); showBubbleAnimation(event);"
                                class="nav-link" href="./assistance_Page_Guide.php">
                                <i class="bi bi-wallet2"></i>
                                <span class="ml-1">Assistance Page</span>
                            </a>
                        </li>

                        <!-- Nav Item - Nurses List Guide -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to patients list page'); showBubbleAnimation(event);"
                                class="nav-link" href="./nurses_List_Guide.php">
                                <i class="fa-solid fa-user-nurse"></i>
                                <span class="ml-1">Nurses List</span>
                            </a>
                        </li>

                        <!-- Nav Item - Patient List Guide -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to nurses list page'); showBubbleAnimation(event);"
                                class="nav-link" href="./patient_List_Guide.php">
                                <i class="bi bi-person-lines-fill"></i>
                                <span class="ml-1">Patient List</span>
                            </a>
                        </li>

                        <!-- Nav Item - Reports Page Guide -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to settings'); showBubbleAnimation(event);"
                                class="nav-link" href="./reports_Guide.php">
                                <i class="fa-solid fa-chart-line"></i>
                                <span class="ml-1">Reports Page</span>
                            </a>
                        </li>

                        <!-- Nav Item - Logs Page Guide -->
                        <li class="nav-item">
                            <a onclick="showSnackbar('redirect to settings'); showBubbleAnimation(event);"
                                class="nav-link" href="./logs_Guide.php">
                                <i class="bi bi-file-ruled"></i>
                                <span class="ml-1">Logs Page</span>
                            </a>
                        </li>
                    </ul>

                </nav>

                <!-- End of Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    <!-- Page content goes here -->
                </div>
            </div>
            <!-- End of Page Content -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="helpContents">

        <main>
            <section id="getting-started">
                <h2>Step 1</h2>
                <p>Step one Contents:
                </p>
            </section>

            <section id="features">
                <h2>Step 2</h2>
                <p>Step two Contents:</p>
            </section>

            <section id="faq">
                <h2>Step 3</h2>
                <p>Step three Contents:</p>
            </section>

            <section id="contact">
                <h2>Step 4</h2>
                <p>Step four Contents:</p>
            </section>
        </main>
    </div>
    <!-- End of Main Content -->


    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!--GARBAGE -->
    <script>
        window.addEventListener('change', event => {
            showSnackbar('added');
        });
    </script>

    <script>
        //Script for searching
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".search-input").forEach((inputField) => {
                const tableRows = inputField
                    .closest("table")
                    .querySelectorAll("tbody > tr");
                const headerCell = inputField.closest("th");
                const otherHeaderCells = headerCell.closest("tr").children;
                const columnIndex = Array.from(otherHeaderCells).indexOf(headerCell);
                const searchableCells = Array.from(tableRows).map(
                    (row) => row.querySelectorAll("td")[columnIndex]
                );

                inputField.addEventListener("input", () => {
                    const searchQuery = inputField.value.toLowerCase();

                    for (const tableCell of searchableCells) {
                        const row = tableCell.closest("tr");
                        const value = tableCell.textContent.toLowerCase().replace(",", "");

                        row.style.visibility = null;

                        if (value.search(searchQuery) === -1) {
                            row.style.visibility = "collapse";
                        }
                    }
                });
            });
        });
    </script>

    <!-- For modal -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
</body>

</html>