package com.digitax.model;

import java.time.LocalDateTime;

import javax.persistence.Column;
import javax.persistence.Entity;
import javax.persistence.GeneratedValue;
import javax.persistence.GenerationType;
import javax.persistence.Id;
import javax.persistence.Table;

import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.annotation.LastModifiedDate;

import lombok.Getter;
import lombok.Setter;



@Entity
@Table(name="relations")
@Getter
@Setter
public class Relation {
  
	@Id
	 @GeneratedValue(strategy = GenerationType.AUTO)
	 private int id;
	 
	 @Column(name = "name")
	 private String name;
	 
	 @Column(name = "is_deleted")
	 private String is_deleted; 
	 
	 
	 @Column(name = "is_active")
	 private int is_active;
	 
	 

	    @CreatedDate
	    @Column(name = "created_at")
	    private LocalDateTime createdAt;

	    @LastModifiedDate
	    @Column(name = "updated_at")
	    private LocalDateTime updatedAt;
	 
}
