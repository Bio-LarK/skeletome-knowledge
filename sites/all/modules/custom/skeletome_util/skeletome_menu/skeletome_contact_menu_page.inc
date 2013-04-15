<?php

function _skeletome_contact_menu() {
  return drupal_get_form('_skeletome_contact_form');
}

function _skeletome_contact_form() {
  $form['contact-investigators-head'] = array(
    '#markup' => '<h2 class="title">Project Investigators</h2>',
    '#weight' => 1
  );

  $form['contact-investigators-az'] = array(
    '#markup' => '<h3>Dr. Andreas Zankl</h3>
		  <h4><a href="http://qbdc.org/home-page/">The Queensland Bone Dysplasia Centre</a>,<br/>
		  UQ Centre for Clinical Research (UQCCR)<br/>
		  The University of Queensland, Building 71/918, Level 3,<br/>
		  Royal Brisbane and Women\'s Hospital Herston, QLD 4029<br/>
		  <b>Email:</b> a.zankl (at) uq.edu.au
		  </h4>',
    '#weight' => 2
  );

  $form['contact-investigators-jh'] = array(
    '#markup' => '<h3>Prof. Jane Hunter</h3>
		  <h4><a href="http://itee.uq.edu.au/~eresearch/">e-Research Group</a>,<br/>
		  School of ITEE, The University of Queensland<br/>
		  Building 78 (GP South), Level 7, Room 709B<br/>
		  St. Lucia, QLD 4072<br/>
		  <b>Email:</b> jane (at) itee.uq.edu.au
		  </h4>',
    '#weight' => 3
  );

  $form['contact-researchers-head'] = array(
    '#markup' => '<h2 class="title">Researchers</h2>',
    '#weight' => 4
  );

  $form['contact-researchers-tg'] = array(
    '#markup' => '<h3>Dr. Tudor Groza</h3>
		  <h4><a href="http://itee.uq.edu.au/~eresearch/">e-Research Group</a>,<br/>
		  School of ITEE, The University of Queensland<br/>
		  Building 78 (GP South), Level 7, Room 709A<br/>
		  St. Lucia, QLD 4072<br/>
		  <b>Responsibilities:</b> Project management, ontology engineering, platform development<br/>
		  <b>Email:</b> tudor.groza (at) uq.edu.au
		  </h4>',
    '#weight' => 5
  );

  $form['contact-researchers-hz'] = array(
    '#markup' => '<h3>Hasti Ziaimatin</h3>
		  <h4><a href="http://itee.uq.edu.au/~eresearch/">e-Research Group</a>,<br/>
		  School of ITEE, The University of Queensland<br/>
		  Building 78 (GP South), Level 7, Room 709A<br/>
		  St. Lucia, QLD 4072<br/>
		  <b>Responsibilities:</b> Ontology engineering, Expertise modelling from micro-contributions<br/>
		  <b>Email:</b> h.ziaimatin (at) uq.edu.au
		  </h4>',
    '#weight' => 6
  );

  $form['contact-researchers-rp'] = array(
    '#markup' => '<h3>Razan Paul</h3>
		  <h4><a href="http://itee.uq.edu.au/~eresearch/">e-Research Group</a>,<br/>
		  School of ITEE, The University of Queensland<br/>
		  Building 78 (GP South), Level 7, Room 709A<br/>
		  St. Lucia, QLD 4072<br/>
		  <b>Responsibilities:</b> Ontology engineering, Semi-automated diagnosis, Disease key features extraction<br/>
		  <b>Email:</b> razan.paul (at) uq.edu.au
		  </h4>',
    '#weight' => 7
  );

/*
  $form['contact-partners-head'] = array(
    '#markup' => '<h2 class="title">External partners</h2>',
    '#weight' => 5
  );
*/
  return $form;
}



