package com.digitax.payload;


import lombok.Getter;
import lombok.Setter;
import lombok.experimental.Accessors;

import java.util.Map;

@Getter
@Setter
@Accessors(chain = true)
public class ApiRes<T>{
    private ResCode co;
    private Map<?, ?> extras;
    private T da;
    private String message;

    private ApiRes() {
    }

    public static ApiRes<?> fail() {
        return new ApiRes<Object>().setCo(ResCode.F);
    }

    public static enum ResCode {
        S, F
    }

    public static <P> ApiRes<P> success(P da) {
        return new ApiRes<P>().setDa(da).setCo(ResCode.S);
    }

	
}
