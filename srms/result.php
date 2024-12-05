<?php
session_start();
error_reporting(E_ALL);
include('includes/config.php');
require_once __DIR__ . '/vendor/autoload.php'; // Include mPDF library

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['USN']) || empty($_POST['class'])) {
        die("<div class='alert alert-danger'>Error: USN or Class ID is missing.</div>");
    }
}

$USN = $_POST['USN'];
$classid = $_POST['class'];
$_SESSION['USN'] = $USN;
$_SESSION['classid'] = $classid;

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $query = "SELECT tblstudents.StudentName, tblstudents.USN, tblstudents.RegDate, tblstudents.StudentId, 
                     tblstudents.Status, tblclasses.Sem, tblclasses.Section 
              FROM tblstudents 
              JOIN tblclasses ON tblclasses.id = tblstudents.ClassId 
              WHERE tblstudents.USN = :USN AND tblstudents.ClassId = :classid";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':USN', $USN, PDO::PARAM_STR);
    $stmt->bindParam(':classid', $classid, PDO::PARAM_STR);
    $stmt->execute();
    $studentDetails = $stmt->fetchAll(PDO::FETCH_OBJ);

    $html = "<div id='student-details' class='panel panel-info'>";
    $html .= "<div class='panel-heading'><h3>Student Details</h3></div>";
    $html .= "<div class='panel-body'>";
    foreach ($studentDetails as $row) {
        $html .= "<p><b>Student Name:</b> " . htmlentities($row->StudentName) . "</p>";
        $html .= "<p><b>USN:</b> " . htmlentities($row->USN) . "</p>";
        $html .= "<p><b>Registration Date:</b> " . htmlentities($row->RegDate) . "</p>";
        $html .= "<p><b>Status:</b> " . htmlentities($row->Status) . "</p>";
        $html .= "<p><b>Semester:</b> " . htmlentities($row->Sem) . "</p>";
        $html .= "<p><b>Section:</b> " . htmlentities($row->Section) . "</p>";
    }
    $html .= "</div></div>";
} catch (PDOException $e) {
    $html = "<div class='alert alert-danger'>Error fetching student details: " . $e->getMessage() . "</div>";
}

try {
    $query = "SELECT t.StudentName, t.USN, t.ClassId, t.marks, SubjectId, tblsubjects.SubjectName 
              FROM (SELECT sts.StudentName, sts.USN, sts.ClassId, tr.marks, SubjectId 
                    FROM tblstudents AS sts 
                    JOIN tblresult AS tr ON tr.StudentId = sts.StudentId) AS t 
              JOIN tblsubjects ON tblsubjects.id = t.SubjectId 
              WHERE t.USN = :USN AND t.ClassId = :classid";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':USN', $USN, PDO::PARAM_STR);
    $stmt->bindParam(':classid', $classid, PDO::PARAM_STR);
    $stmt->execute();
    $resultDetails = $stmt->fetchAll(PDO::FETCH_OBJ);

    $html .= "<div id='result-details' class='panel panel-success'>";
    $html .= "<div class='panel-heading'><h3>Result Details</h3></div>";
    $html .= "<div class='panel-body'>";
    $html .= "<table class='table table-bordered'>";
    $html .= "<thead><tr><th>#</th><th>Subject</th><th>Marks</th></tr></thead>";
    $html .= "<tbody>";

    $cnt = 1;
    $totalMarks = 0;
    foreach ($resultDetails as $result) {
        $totalMarks += $result->marks;
        $html .= "<tr>";
        $html .= "<td>" . htmlentities($cnt) . "</td>";
        $html .= "<td>" . htmlentities($result->SubjectName) . "</td>";
        $html .= "<td>" . htmlentities($result->marks) . "</td>";
        $html .= "</tr>";
        $cnt++;
    }
    $percentage = ($totalMarks / (($cnt - 1) * 100)) * 100;

    $html .= "<tr><th colspan='2'>Total Marks</th><td>" . htmlentities($totalMarks) . "</td></tr>";
    $html .= "<tr><th colspan='2'>Percentage</th><td>" . htmlentities(number_format($percentage, 2)) . "%</td></tr>";
    $html .= "</tbody></table>";
    $html .= "</div></div>";
} catch (PDOException $e) {
    $html .= "<div class='alert alert-danger'>Error fetching results: " . $e->getMessage() . "</div>";
}

echo $html;

echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<button onclick='downloadPDF()' class='btn btn-primary'>Download Results as PDF</button>";
echo "</div>";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const element = document.body;

    html2canvas(element).then(function (canvas) {
        const pdf = new jsPDF();
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 190; // A4 page width in mm
        const pageHeight = 285; // A4 page height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        let heightLeft = imgHeight;

        let position = 0;
        pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('student_data.pdf');
    });
}
</script>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 20px;
}

.panel {
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.panel-heading {
    background-color: #007bff;
    color: white;
    padding: 10px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
}

.panel-body {
    padding: 20px;
    background-color: white;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border: 1px solid #dee2e6;
}

.table th {
    background-color: #007bff;
    color: white;
}

.table tbody tr:nth-child(odd) {
    background-color: #f2f2f2;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    color: white;
    padding: 10px 20px;
    cursor: pointer;
}
</style>
