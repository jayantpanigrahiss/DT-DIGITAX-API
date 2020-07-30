package com.digitax.controller;

import java.util.HashSet;
import java.util.List;
import java.util.Set;
import java.util.stream.Collectors;

import javax.validation.Valid;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import com.digitax.model.ERole;
import com.digitax.model.Role;
import com.digitax.model.User;
import com.digitax.payload.ApiRes;
import com.digitax.payload.request.LoginRequest;
import com.digitax.payload.request.SignupRequest;
import com.digitax.payload.response.JwtResponse;
import com.digitax.repository.RoleRepository;
import com.digitax.repository.UserRepository;
import com.digitax.security.jwt.AuthEntryPointJwt;
import com.digitax.security.jwt.JwtUtils;
import com.digitax.security.services.UserDetailsImpl;

@CrossOrigin(origins = "*", maxAge = 3600)
@RestController
@RequestMapping("/api/auth")
public class AuthController {
	private static final Logger logger = LoggerFactory.getLogger(AuthEntryPointJwt.class);
	
	@Autowired
	AuthenticationManager authenticationManager;

	@Autowired
	UserRepository userRepository;

	@Autowired
	RoleRepository roleRepository;

	@Autowired
	PasswordEncoder encoder;

	@Autowired
	JwtUtils jwtUtils;

	@PostMapping("/signin")
	public ResponseEntity<?> authenticateUser(@Valid @RequestBody LoginRequest loginRequest) {
		 try {
		Authentication authentication = authenticationManager.authenticate(
				new UsernamePasswordAuthenticationToken(loginRequest.getUsername(), loginRequest.getPassword()));

		SecurityContextHolder.getContext().setAuthentication(authentication);
		String jwt = jwtUtils.generateJwtToken(authentication);
		
		UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();		
		List<String> roles = userDetails.getAuthorities().stream()
				.map(item -> item.getAuthority())
				.collect(Collectors.toList());

		Object obj = new JwtResponse(jwt, 
							 userDetails.getId(), 
							 userDetails.getUsername(), 
							 userDetails.getEmail(), 
							 roles);
		 return new ResponseEntity<>(ApiRes.success(obj).setMessage("LogIn Success."), HttpStatus.OK);
		 } catch (Exception ex) {
			 
			 //logger.error("Unauthorized user.");
             return ResponseEntity.status(HttpStatus.OK).body(ApiRes.fail().setMessage("Unauthorized user."));
	        }
	}

	@PostMapping("/signup")
	public ResponseEntity<?> registerUser(@Valid @RequestBody SignupRequest signUpRequest) {
		if (userRepository.existsByUsername(signUpRequest.getUsername())) {
			return ResponseEntity.status(HttpStatus.OK).body(ApiRes.fail().setMessage("Username is already taken!"));
			
		}

		if (userRepository.existsByEmail(signUpRequest.getEmail())) {
			return ResponseEntity.status(HttpStatus.OK).body(ApiRes.fail().setMessage("Email is already in use!"));
			
		 }
		
		if (userRepository.existsByPhone(signUpRequest.getPhone())) {
			return ResponseEntity.status(HttpStatus.OK).body(ApiRes.fail().setMessage("Phone is already in use!"));
			
		 }

		// Create new user's account
		User user = new User(signUpRequest.getUsername(), 
							 signUpRequest.getEmail(),
							 encoder.encode(signUpRequest.getPassword()),
							 signUpRequest.getPhone());
        
		Set<String> strRoles = signUpRequest.getRole();
		Set<Role> roles = new HashSet<>();
      
		if (strRoles == null) {
			Role userRole = roleRepository.findByName(ERole.ROLE_USER)
					.orElseThrow(() -> new RuntimeException("Error: Role is not found."));
			roles.add(userRole);
		} else {
			strRoles.forEach(role -> {
				switch (role) {
				case "admin":
					Role adminRole = roleRepository.findByName(ERole.ROLE_ADMIN)
							.orElseThrow(() -> new RuntimeException("Error: Role is not found."));
					roles.add(adminRole);

					break;
				case "mod":
					Role modRole = roleRepository.findByName(ERole.ROLE_MODERATOR)
							.orElseThrow(() -> new RuntimeException("Error: Role is not found."));
					roles.add(modRole);

					break;
				default:
					Role userRole = roleRepository.findByName(ERole.ROLE_USER)
							.orElseThrow(() -> new RuntimeException("Error: Role is not found."));
					roles.add(userRole);
				}
			});
		}

		 user.setRoles(roles);
		 userRepository.save(user);
		 try {
				Authentication authentication = authenticationManager.authenticate(
						new UsernamePasswordAuthenticationToken(signUpRequest.getUsername(), signUpRequest.getPassword()));

				SecurityContextHolder.getContext().setAuthentication(authentication);
				String jwt = jwtUtils.generateJwtToken(authentication);
				
				UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();		
				List<String> roles1 = userDetails.getAuthorities().stream()
						.map(item -> item.getAuthority())
						.collect(Collectors.toList());

				Object obj = new JwtResponse(jwt, 
									 userDetails.getId(), 
									 userDetails.getUsername(), 
									 userDetails.getEmail(), 
									 roles1);
					 return new ResponseEntity<>(ApiRes.success(obj).setMessage("Sign Up Success."), HttpStatus.OK);
					 } catch (Exception ex) {
					 
					 logger.error("Unauthorized error: {}");
		             return ResponseEntity.status(HttpStatus.OK).body(ApiRes.fail().setMessage("Something went wrong."));
			        }
	}
}