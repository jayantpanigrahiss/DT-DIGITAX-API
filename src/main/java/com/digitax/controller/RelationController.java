package com.digitax.controller;

import java.util.ArrayList;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import com.digitax.repository.RelationRepository;
import com.digitax.service.RelationService;
import com.digitax.model.Relation;
import com.digitax.payload.ApiRes;


@RestController
public class RelationController {
	
	 @Autowired
	 RelationService relationService;
	 
	 @Autowired
	 RelationRepository relationRepository;
	 
	 
	    @GetMapping("/get/relations")
	    public ResponseEntity<?>  getRelations(){
	    	ArrayList<Relation> obj = new ArrayList<>(relationService.getRelation()); 
			 return new ResponseEntity<>(ApiRes.success(obj).setMessage("List Relation"), HttpStatus.OK);
		 }

}
