<?php

    define("_VALID_PHP", true);

    include 'include/connexion_BD.php';
    include 'include/fonctions.php';
// Include database connection and functions here.
    sec_session_start();
    if(login_check($bdd) == true) {

        if(!isAdmin($bdd)) header('location:index.php');
        $CurYear_des=get_cur_year("des",$bdd);
        $CurYear=get_cur_year("id",$bdd);

/*******************************************************************/
        define( "COL_MIN_AVG", 64 );
        define( "COL_MAX_AVG", 255 );
        define( "COL_STEP", 16 );

// (192 - 64) / 16 = 8
// 8 ^ 3 = 512 colors

        function usercolor( $username ) {
            $range = COL_MAX_AVG - COL_MIN_AVG;
            $factor = $range / 256;
            $offset = COL_MIN_AVG;

            $base_hash = substr(md5($username), 0, 6);
            $b_R = hexdec(substr($base_hash,0,2));
            $b_G = hexdec(substr($base_hash,2,2));
            $b_B = hexdec(substr($base_hash,4,2));

            $f_R = floor((floor($b_R * $factor) + $offset) / COL_STEP) * COL_STEP;
            $f_G = floor((floor($b_G * $factor) + $offset) / COL_STEP) * COL_STEP;
            $f_B = floor((floor($b_B * $factor) + $offset) / COL_STEP) * COL_STEP;

            return sprintf('%02x%02x%02x', $f_R, $f_G, $f_B);
        }

        /*******************************************************************/

     //   if(!empty($_GET['epreuve']) && $_GET['epreuve'] == (sanitize($_GET['epreuve'],false,true)))
        {

        //    echo '<table><tr><th>Module</th><th>Element</th><th>type</th><th>groupes</th><th>hrs_TD</th><th>Enseigant</th><th>grade</th><th>departement</th></tr>';

            /** PHPExcel */
            include_once 'include/PHPExcel.php';

            /** PHPExcel_Writer_Excel2007 */
            include_once 'include/PHPExcel/Writer/Excel2007.php';

            // Create new PHPExcel object
            //    echo date('H:i:s') . " Create new PHPExcel object\n";
            $objPHPExcel = new PHPExcel();

            // Set properties
            //    echo date('H:i:s') . " Set properties\n";
            $objPHPExcel->getProperties()->setCreator("Gestion des services - FSTF");
            //   $objPHPExcel->getProperties()->setLastModifiedBy("FSTF");
            $objPHPExcel->getProperties()->setTitle("Repartition des enseignements");
            $objPHPExcel->getProperties()->setSubject("FSTF - Departement Informatique");
            $objPHPExcel->getProperties()->setDescription("Affectation genere par l'application de gestion");

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle('Info '.$CurYear_des);

            //columns width
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(70);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(39);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(13);
     //       $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(12);

            $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');
            $objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');
            $objPHPExcel->getActiveSheet()->mergeCells('A3:Q3');
            $objPHPExcel->getActiveSheet()->mergeCells('A4:Q4');
            $objPHPExcel->getActiveSheet()->mergeCells('A5:Q5');
            $objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');

            $objPHPExcel->getActiveSheet()->getStyle('A1:A6')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F:Q')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A8:Q8')
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'UNIVERSITE SIDI MOHAMED BEN ABDELLAH');
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'FACULTE DES SCIENCES ET TECHNIQUES DE FES');
            $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Département d\'Informatique - '.$CurYear_des);

            $objPHPExcel->getActiveSheet()->getStyle('A:Q')->getFont()->setBold(true)->setName("Times New Roman");

            $objPHPExcel->getActiveSheet()->getStyle('A4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0');
            $objPHPExcel->getActiveSheet()->getStyle('H8:Q8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('CCFFFF');

            $objPHPExcel->getActiveSheet()->SetCellValue('A8', 'Cycle');
            $objPHPExcel->getActiveSheet()->SetCellValue('B8', 'Filière');
            $objPHPExcel->getActiveSheet()->SetCellValue('C8', 'Semestre');
            $objPHPExcel->getActiveSheet()->SetCellValue('D8', 'Module');
            $objPHPExcel->getActiveSheet()->SetCellValue('E8', 'Elément de module');
            $objPHPExcel->getActiveSheet()->SetCellValue('F8', 'Département d\'attache');
            $objPHPExcel->getActiveSheet()->SetCellValue('G8', "Type d'enseignement");

            $objPHPExcel->getActiveSheet()->SetCellValue('H8', 'Nom');
            $objPHPExcel->getActiveSheet()->SetCellValue('I8', 'Prénom');
            $objPHPExcel->getActiveSheet()->SetCellValue('J8', 'Nombre total de sections ou de groupes');
            $objPHPExcel->getActiveSheet()->SetCellValue('K8', 'Nombre de section ou de groupe affecté');
     //       $objPHPExcel->getActiveSheet()->SetCellValue('L8', 'VH (HTD)');
            $objPHPExcel->getActiveSheet()->SetCellValue('L8', 'VH');
            $objPHPExcel->getActiveSheet()->SetCellValue('M8', 'P/V');
            $objPHPExcel->getActiveSheet()->SetCellValue('N8', 'Grade');
            $objPHPExcel->getActiveSheet()->SetCellValue('O8', 'Etablissement');
            $objPHPExcel->getActiveSheet()->SetCellValue('P8', 'Total');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q8', 'Perequation');

            $objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(75);


            $objPHPExcel->getActiveSheet()->getStyle('A8:Q8')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    //        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray( array( 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000') )  )  );

            $i=8;
            $F="";
            $F_NB=0;
            $Color=usercolor($F_NB);
            if($stmt = $bdd->prepare("SELECT   MD.`periode`, MD.`moduleID`, MD.`module_DetailsID`, MD.`grp_td`, MD.`grp_tp`, M.designation, M.semestre, F.`designation`, C.`designation` FROM module_details AS MD LEFT JOIN `module` M ON M.`moduleID`= MD.`moduleID` LEFT JOIN `filiere`  AS F ON F.`filiereID`=M.`filiereID` LEFT JOIN `cycle` AS C ON C.`cycleID`=F.`cycleID` WHERE MD.`annee_UniversitaireID`=? ORDER BY C.`cycleID`,F.`filiereID`,M.semestre,MD.`periode`"))
            {
                $stmt->bind_param("i",$CurYear);
                $stmt->execute();
                $stmt->bind_result($periode,$mod_id,$MD_id,$td,$tp,$module,$semestre,$filiere,$cycle);
                $stmt->store_result();

                if($stmt->num_rows >0)
                {

                    while($stmt->fetch())
                    {

                        if($stmt1 = $bdd->prepare("SELECT EMD.`element_ModuleID` , EMD.`element_Module_DetailsID` ,EMD.`grp_cours`,EMD.`grp_td`, EMD.`grp_tp`, EM.designation, EM.`heures_cours`, EM.`heures_td`, EM.`heures_tp`, D.`designation` FROM `element_module_details` AS EMD LEFT JOIN `element_module` AS EM ON EMD.`element_ModuleID` = EM.`element_ModuleID` LEFT JOIN `departement` AS D ON EM.`departementID`=D.`departementID` WHERE `module_DetailsID` =?"))
                        {
                            $stmt1->bind_param("i",$MD_id);
                            $stmt1->execute();
                            $stmt1->bind_result($EM_id,$EMD_id,$EMD_grp_cours,$EMD_grp_td, $EMD_grp_tp, $elem_mod,$hrs_cours,$hrs_td,$hrs_tp,$departement);
                            $stmt1->store_result();

                            if($stmt1->num_rows >0)
                            {

                                while($stmt1->fetch())
                                {
                                    if($stmt2 = $bdd->prepare("SELECT A.partage, A.`enseignantID`, A.`affectationID`,A.`nature`, A.`groups`, A.`auto`, E.`email`, E.`grade`, E.`nom`, E.`prenom`, E.`departementID`, E.`vacataire`, D.`designation` FROM `affectation` AS A LEFT JOIN `enseignant` AS E ON E.`enseignantID`=A.`enseignantID` LEFT JOIN `grade` AS G ON G.`gradeID`=E.`grade` LEFT JOIN `departement` AS D ON D.`departementID`=E.`departementID` WHERE A.`element_Module_DetailsID`=? ORDER BY A.`nature`"))
                                    {
                                        $stmt2->bind_param("i",$EMD_id);
                                        $stmt2->execute();
                                        $stmt2->bind_result($A_partage,$E_id,$A_id,$A_nature,$A_grp,$A_auto,$E_email,$E_grade,$E_nom,$E_prenom,$E_dept,$E_vac,$D_designation);
                                        $stmt2->store_result();

                                        if($stmt2->num_rows >0)
                                        {

                                            while($stmt2->fetch())
                                            {
                                                $E_grade=getValue('grade','enseignant_actif','`enseignant`='.$E_id.' AND `annee`='.$CurYear,$bdd);//(SELECT  `grade` FROM `enseignant_actif` WHERE `enseignant`='.$_GET['prof'].' AND `annee`='.$cur_year.') AS `grade`
                                                $G_code=getValue('code','grade','gradeID='.$E_grade,$bdd);


                                                $i++;
                                                $hrs=0;
                                              //  $hrs_TD=0;
                                                $coef=0;
                                                $total_grp=0;
                                                if($A_nature=="cours")
                                                {
                                                    $hrs=$hrs_cours;
                                                   // $hrs_TD=$hrs_cours;//*H_CM;
                                                    $coef = H_CM;
                                                    $total_grp=$EMD_grp_cours;
                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF99CC');

                                                }
                                                elseif($A_nature=="TD")
                                                {
                                                    $hrs=$hrs_td;
                                                    //$hrs_TD=$hrs_td;//*H_TD;
                                                    $coef = H_TD;
                                                    $total_grp=$EMD_grp_td;
                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00CCFF');
                                                }
                                                elseif($A_nature=="TP")
                                                {
                                                    $hrs=$hrs_tp;
                                                    //$hrs_TD=$hrs_tp;//*H_TP;
                                                    $coef = H_TP;
                                                    $total_grp=$EMD_grp_tp;
                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF8080');
                                                }

                                                if($E_vac==1)
                                                {
                                                    $PV="Vacataire";
                                                    $etablissement="";
                                                }else
                                                {
                                                    $etablissement="FSTF";
                                                    $PV=$D_designation;
                                                }

                                        //        echo "<tr><th>$module</th><th>$elem_mod</th><th>$A_nature</th><th>$A_grp</th><th>$hrs</th><th>$E_nom $E_prenom</th><th>$G_code</th><th>$D_designation</th></tr>";

                                                if($F!=$filiere)
                                                {
                                                    $F=$filiere;
                                                    $F_NB++;
                                                    $Color=usercolor($F_NB);
                                                }

                                                if($A_partage!=0)
                                                {
                                                    $j=0;
                                                    if($stmt5 = $bdd->prepare("SELECT  E.`enseignantID` ,E.`email`, E.`nom`, E.`prenom`, E.`departementID`, E.`vacataire`, D.`designation` FROM `affectation_partage` AS A LEFT JOIN `enseignant` AS E ON E.`enseignantID`=A.`enseignantID` LEFT JOIN `departement` AS D ON D.`departementID`=E.`departementID` WHERE A.`affectationID`=?")) {
                                                        $stmt5->bind_param('i',$A_id);
                                                        $stmt5->execute();
                                                        $stmt5->bind_result($E_ID,$E_email,$E_nom,$E_prenom,$E_dept,$E_vac,$D_designation);
                                                        $stmt5->store_result();

                                                        if ($stmt5->num_rows > 0) {

                                                     //       $G_code=0;
                                                            $E_grade=getValue('grade','enseignant_actif','`enseignant`='.$E_ID.' AND `annee`='.$CurYear,$bdd);//(SELECT  `grade` FROM `enseignant_actif` WHERE `enseignant`='.$_GET['prof'].' AND `annee`='.$cur_year.') AS `grade`
                                                      //      $chargeHrs =getValue('chargeHrs','grade_actif','grade='.$E_grade.' AND `annee`='.$CurYear,$bdd); // (SELECT `chargeHrs` from `grade_actif` AS GA WHERE GA.`grade`=E.grade AND `annee`='.$cur_year.')As `chargeHrs`
                                                            $G_code=getValue('code','grade','gradeID='.$E_grade,$bdd);

                                                            $hrs/=$stmt5->num_rows;
                                                         //   $hrs_TD/=$stmt5->num_rows;

                                                            $begin = $i;
                                                          //  $col = 0;//$Color=usercolor($i);
                                                            while ($stmt5->fetch()) {
                                                                $j++;


                                                                if($E_vac==1)
                                                                {
                                                                    $PV="Vacataire";
                                                                    $etablissement="";
                                                                }else
                                                                {
                                                                    $etablissement="FSTF";
                                                                    $PV=$D_designation;
                                                                }

                                                                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':F' . $i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($Color);
                                                                $objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':Q' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


                                                         //       $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':R'.$i)->getFont()->getColor()->setARGB($col);

                                                                if($A_nature=="cours")
                                                                {
                                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF99CC');
                                                                }
                                                                elseif($A_nature=="TD")
                                                                {
                                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00CCFF');
                                                                }
                                                                elseif($A_nature=="TP")
                                                                {
                                                                    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':Q'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF8080');
                                                                }


                                                            //    $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':R'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00CCFF');
                                                                //$objPHPExcel->getActiveSheet()->getStyle('G' . $i . ':R' . $i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
                                                              //  $objPHPExcel->getActiveSheet()->getStyle('G'.$i.':R'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF8080');


                                                                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, $cycle);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, $filiere);
                                                                $temp = "Semestre " . $semestre . " - " . get_periode_name($periode);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, $temp);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, $module);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, $elem_mod);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, $departement);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, $A_nature);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, $E_nom);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, $E_prenom);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, $total_grp);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, $A_grp);
                                                         //       $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "=M$i*$coef");
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, $hrs);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, $PV);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, $G_code);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, $etablissement);
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "=K$i*L$i");//$hrs * $A_grp
                                                                $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, perequationDetail($A_nature,$E_vac,$i)); //perequation($A_nature,$G_code,$E_vac,$i,"Q")

                                                                if($j<$stmt5->num_rows)
                                                                $i++;
                                                            }
                                                            $objPHPExcel->getActiveSheet()->getStyle('G' . $begin . ':Q' . $i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUMDASHED);
                                                            $objPHPExcel->getActiveSheet()->getStyle('G' . $begin . ':Q' . $i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUMDASHED);
                                                            $objPHPExcel->getActiveSheet()->getStyle('G' . $begin . ':Q' . $i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUMDASHED);
                                                            $objPHPExcel->getActiveSheet()->getStyle('G' . $begin . ':Q' . $i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUMDASHED);
                                                        }
                                                    }
                                                }else{


                                                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB($Color);
                                                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                                                        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $cycle);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $filiere);
                                                        $temp="Semestre ".$semestre." - ".get_periode_name($periode);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $temp);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $module);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $elem_mod);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $departement);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $A_nature);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $E_nom);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $E_prenom);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $total_grp);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $A_grp);
                                                //        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, "=M$i*$coef");
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $hrs);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $PV);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $G_code);
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$i,$etablissement );
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$i, "=K$i*L$i");
                                                        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, perequationDetail($A_nature,$E_vac,$i));
                                                }



                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
            }
//            echo '</table>';

       //     $objPHPExcel->getActiveSheet()->setTitle('Simple');


            // Save Excel 2007 file
       //     echo date('H:i:s') . " Write to Excel2007 format\n";
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
       //     $objWriter->

            $file_name=__DIR__."/include/temp/".time()."xlsx";

            $objWriter->save($file_name);


            header('Content-Disposition: attachment; filename=' . 'Affectations_DI_FSTF_'.$CurYear_des.'.xlsx' );
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Length: ' . filesize($file_name));
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            readfile($file_name);

            unlink($file_name);
        }













    } else {
        header('location:login.php');
    }

function perequationDetail($type,$vac,$ligne,$total_col="P",$grade_col="N")
{
    $ret = "";
    $total_eq= "=$total_col$ligne";
    if($vac==1)
    {
        $ret= $total_eq;
    }else{
        $TCOL = "$total_col$ligne";
        $GCOL = "$grade_col$ligne";
        if($type=="cours")
        {
            $ret = '=IF('.$GCOL.'="PES",'.$TCOL.',IF('.$GCOL.'="PH",'.$TCOL.','.$TCOL.'*1.5))';
        }elseif($type=="TD")
        {
            $ret = '=IF('.$GCOL.'="PES",'.$TCOL.'/1.5,IF('.$GCOL.'="PH",'.$TCOL.'/1.5,'.$TCOL.'))';
        }elseif($type=="TP")
        {
            $ret = '=IF('.$GCOL.'="PES",'.$TCOL.'/2,IF('.$GCOL.'="PH",'.$TCOL.'/2,'.$TCOL.'*1.5/2))';
        }
    }
    return $ret;
}

function perequation($type,$grade,$vac,$ligne,$total_col)
{
    $ret = "";
    $total_eq= "=$total_col$ligne";
 if($vac==1)
 {
     $ret= $total_eq;
 }else{
     if($type=="cours")
     {
         if($grade=="PES")
             $ret=$total_eq;
         elseif($grade=="PH")
             $ret=$total_eq;
         else
             $ret=$total_eq."*1.5";
     }elseif($type=="TD")
     {
         if($grade=="PES")
             $ret=$total_eq."/1.5";
         elseif($grade=="PH")
             $ret=$total_eq."/1.5";
         else
             $ret=$total_eq;
     }elseif($type=="TP")
     {
         if($grade=="PES")
             $ret=$total_eq."/2";
         elseif($grade=="PH")
             $ret=$total_eq."/2";
         else
             $ret=$total_eq."*1.5/2";
     }
 }
    return $ret;
}
?>