function productImage(pro){
    var params = JSON.parse(pro.replace(/\n/g,"\\n"));
    var path = filePath();
    for(var item in params){
        if(params[item].cartProduct != null){
            var products = params[item].cartProduct.products;
            $('.product-image').attr({"w":products.width,"h":products.height}).css({"width":140+'px',"position":"absolute"});
            if(params[item].cartProduct.only_core === true){
                if(!isNull(products.productCores)){
                    var core = products.productCores[0].core;
                    var coreX = products.productCores[0].start_x;
                    var coreY = products.productCores[0].start_y;
                    if(!isNull(products.productBorders)){
                        var border = products.productBorders[0].border;
                        var coreW = (core.width * 140 / products.width).toFixed(3);
                        var coreH = (core.height * 140 / products.height).toFixed(3);
                        if(!isNull(border.borderPattern) && !isNull(border.borderPattern[0].borderPatternDemi)){
                            var demiBorder = border.borderPattern[0].borderPatternDemi[0].demiBorder;
                            var borderInWidth = demiBorder.line_out_width - demiBorder.pressure_draw_width;
                            var lineInWidth = (borderInWidth * 140 /products.width).toFixed(3);
                        }
                    }
                }
                $('.product-image[data="'+products.id+'"] .coreImg').attr({"data":products.productCores[0].id,"w":core.width,"h":core.height,"x":parseFloat(coreX) + borderInWidth,"y":parseFloat(coreY) +borderInWidth})
                    .css({"width":coreW+'px',"height":coreH+'px',"top":lineInWidth+'px',"left":lineInWidth+'px',"z-index":1,"position":"absolute"});
                $('.product-image[data="'+products.id+'"] .coreImg img').attr("src",path+core.productImageNorms[0].uri);
                $('.product-image[data="'+products.id+'"] .borderImg').hide();
                $('.product-image[data="'+products.id+'"] .frameImg').hide();

                if(!isNull(products) && products.length != 0){
                    if(!isNull(products.name) && products.name.length != 0){
                        if(!isNull(products.name.description) && products.name.description.length != 0){
                            if(!isNull(products.name.description.content)){
                                var productName = products.name.description.content;
                                $('.info-text p[name="name"] span').text(productName).attr("data",productName);
                            }else{
                                $('.info-text p[name="name"] span').text("无");
                            }
                        }
                    }
                    if(!isNull(products.width) && !isNull(products.height)){
                        var productSize = products.width+"x"+products.height+"mm";
                        $('.info-text p[name="product-size"] span').attr("data",productSize).text(productSize);
                    }else{
                        $('.info-text p[name="product-size"] span').text("无");
                    }
                    if(!isNull(products.productCores) && products.productCores.length != 0){
                        for(var a in products.productCores){
                            if(!isNull(products.productCores[a].core) && products.productCores[a].core.length != 0){
                                var core1 = products.productCores[a].core;
                                if(!isNull(core1.width) && !isNull(core1.height)){
                                    var coreSize = core1.width+"x"+core1.height+"mm";
                                    $('.info-text p[name="core-size"] span').text(coreSize).attr("data",coreSize);
                                }else{
                                    $('.info-text p[name="core-size"] span').text("无");
                                }
                                if(!isNull(core1.corePattern) && core1.corePattern.length != 0){
                                    for(var b in core1.corePattern){
                                        if(!isNull(core1.corePattern[b].pattern) && core1.corePattern[b].pattern != 0){
                                            if(!isNull(core1.corePattern[b].pattern.name)){
                                                var corePattern = core1.corePattern[b].pattern.name;
                                                $('.info-text p[name="core-pattern"] span').text(corePattern).attr("data",corePattern);
                                            }else{
                                                $('.info-text p[name="core-pattern"] span').text("无");
                                            }
                                        }
                                        if(!isNull(core1.corePattern[b].corePatternDemi) && core1.corePattern[b].corePatternDemi.length != 0){
                                            for(var c in core1.corePattern[b].corePatternDemi){
                                                var demiCore = core1.corePattern[b].corePatternDemi[c];
                                                if(!isNull(demiCore.demiCore) && demiCore.demiCore != 0){
                                                    if(!isNull(demiCore.demiCore.name)){
                                                        var coreMaterial = demiCore.demiCore.name;
                                                        $('.info-text p[name="core-material"] span').text(coreMaterial).attr("data",coreMaterial);
                                                    }else{
                                                        $('.info-text p[name="core-material"] span').text("无");
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
            }else{
                if(!isNull(products.productBorders) && products.productBorders != 0){
                    var border = products.productBorders[0].border;
                    var borderX = products.productBorders[0].start_x;
                    var borderY = products.productBorders[0].start_y;
                    if(!isNull(border.borderPattern) && !isNull(border.borderPattern[0].borderPatternDemi)){
                        var demiBorder = border.borderPattern[0].borderPatternDemi[0].demiBorder;
                        var borderInWidth = demiBorder.line_out_width - demiBorder.pressure_draw_width;
                        var lineInWidth = (parseFloat(borderInWidth) * 140 / parseFloat(products.width)).toFixed(3);
                    }
                    $('.product-image[data="'+products.id+'"] .borderImg').attr({"data":products.productBorders[0].border_id,"w":border.width,"h":border.height,"x":borderX,"y":borderY})
                        .css({"width":140+'px',"z-index":3,"position":"absolute"});
                    $('.product-image[data="'+products.id+'"] .borderImg img').attr("src",path+border.productImageNorms[0].uri);
                }
                if(!isNull(products.productFrames) && products.productFrames.length !== 0){
                    var frame = products.productFrames[0].frame;
                    var frameX = products.productFrames[0].start_x;
                    var frameY = products.productFrames[0].start_y;
                    var frameW = (parseFloat(frame.width) * 140 / parseFloat(products.width)).toFixed(3);
                    var frameH = (parseFloat(frame.height) * 140 / parseFloat(products.height)).toFixed(3);
                    $('.product-image[data="'+products.id+'"] .frameImg').attr({"data":products.productFrames[0].id,"w":frame.width,"h":frame.height,"x":parseFloat(frameX)+borderInWidth,"y":parseFloat(frameY)+borderInWidth})
                        .css({"width":frameW+'px',"height":frameH+'px',"top":lineInWidth+'px',"left":lineInWidth+'px',"z-index":2,"position":"absolute"});
                    $('.product-image[data="'+products.id+'"] .frameImg img').attr("src",path+frame.productImageNorms[0].uri);
                }
                if(!isNull(products.productCores) && products.productCores.length !== 0){
                    var core = products.productCores[0].core;
                    var coreX = products.productCores[0].start_x;
                    var coreY = products.productCores[0].start_y;
                    var coreW = (parseFloat(core.width) * 140 / parseFloat(products.width)).toFixed(3);
                    var coreH = (parseFloat(core.height) * 140 / parseFloat(products.height)).toFixed(3);
                    $('.product-image[data="'+products.id+'"] .coreImg').attr({"data":products.productCores[0].id,"w":core.width,"h":core.height,"x":parseFloat(coreX)+borderInWidth,"y":parseFloat(coreY)+borderInWidth})
                        .css({"width":coreW+'px',"height":coreH+'px',"top":lineInWidth+'px',"left":lineInWidth+'px',"z-index":1,"position":"absolute"});
                    $('.product-image[data="'+products.id+'"] .coreImg img').attr("src",path+core.productImageNorms[0].uri);
                }

                if(!isNull(products) && products.length != 0){
                    if(!isNull(products.name) && products.name.length != 0){
                        if(!isNull(products.name.description) && products.name.description.length != 0){
                            if(!isNull(products.name.description.content)){
                                var productName = products.name.description.content;
                                $('.info-text p[name="name"] span').text(productName).attr("data",productName);
                            }else{
                                $('.info-text p[name="name"] span').text("无");
                            }
                        }
                    }
                    if(!isNull(products.width) && !isNull(products.height)){
                        var productSize = products.width+"x"+products.height+"mm";
                        $('.info-text p[name="product-size"] span').attr("data",productSize).text(productSize);
                    }else{
                        $('.info-text p[name="product-size"] span').text("无");
                    }
                    if(!isNull(products.productCores) && products.productCores.length != 0){
                        for(var a in products.productCores){
                            if(!isNull(products.productCores[a].core) && products.productCores[a].core.length != 0){
                                var core1 = products.productCores[a].core;
                                if(!isNull(core1.width) && !isNull(core1.height)){
                                    var coreSize = core1.width+"x"+core1.height+"mm";
                                    $('.info-text p[name="core-size"] span').text(coreSize).attr("data",coreSize);
                                }else{
                                    $('.info-text p[name="core-size"] span').text("无");
                                }
                                if(!isNull(core1.corePattern) && core1.corePattern.length != 0){
                                    for(var b in core1.corePattern){
                                        if(!isNull(core1.corePattern[b].pattern) && core1.corePattern[b].pattern != 0){
                                            if(!isNull(core1.corePattern[b].pattern.name)){
                                                var corePattern = core1.corePattern[b].pattern.name;
                                                $('.info-text p[name="core-pattern"] span').text(corePattern).attr("data",corePattern);
                                            }else{
                                                $('.info-text p[name="core-pattern"] span').text("无");
                                            }
                                        }
                                        if(!isNull(core1.corePattern[b].corePatternDemi) && core1.corePattern[b].corePatternDemi.length != 0){
                                            for(var c in core1.corePattern[b].corePatternDemi){
                                                var demiCore = core1.corePattern[b].corePatternDemi[c];
                                                if(!isNull(demiCore.demiCore) && demiCore.demiCore != 0){
                                                    if(!isNull(demiCore.demiCore.name)){
                                                        var coreMaterial = demiCore.demiCore.name;
                                                        $('.info-text p[name="core-material"] span').text(coreMaterial).attr("data",coreMaterial);
                                                    }else{
                                                        $('.info-text p[name="core-material"] span').text("无");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!isNull(products.mount) && products.mount != 0){
                        if(!isNull(products.mount.name)){
                            var mount = products.mount.name;
                            $('.info-text p[name="mount"] span').text(mount).attr("data",mount);
                        }else{
                            $('.info-text p[name="mount"] span').text("无");
                        }
                    }
                    if(!isNull(products.productBorders) && products.productBorders != 0){
                        for(var i in products.productBorders){
                            if(!isNull(products.productBorders[i].border) && products.productBorders[i].border != 0){
                                var border1 = products.productBorders[i].border;
                                if(!isNull(border1.borderPattern) && border1.borderPattern != 0){
                                    for(var j in border1.borderPattern){
                                        if(!isNull(border1.borderPattern[j].borderPatternDemi) && border1.borderPattern[j].borderPatternDemi != 0){
                                            for(var k in border1.borderPattern[j].borderPatternDemi){
                                                var demiBorder1 = border1.borderPattern[j].borderPatternDemi[k];
                                                if(!isNull(demiBorder1.demiBorder) && demiBorder1.demiBorder != 0){
                                                    if(!isNull(demiBorder1.demiBorder.name)){
                                                        var borderName = demiBorder1.demiBorder.name;
                                                        $('.info-text p[name="border"] span').text(borderName).attr("data",borderName);
                                                    }else{
                                                        $('.info-text p[name="border"] span').text("无");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!isNull(products.productFrames) && products.productFrames != 0){
                        for(var m in products.productFrames){
                            if(!isNull(products.productFrames[m].frame) && products.productFrames[m].frame != 0){
                                var frame1 = products.productFrames[m].frame;
                                if(!isNull(frame1.framePattern) && frame1.framePattern != 0){
                                    for(var n in frame1.framePattern){
                                        if(!isNull(frame1.framePattern[n].framePatternDemi) && frame1.framePattern[n].framePatternDemi != 0){
                                            for(var o in frame1.framePattern[n].framePatternDemi){
                                                var demiFrame1 = frame1.framePattern[n].framePatternDemi[o];
                                                if(!isNull(demiFrame1.demiFrame) && demiFrame1.demiFrame != 0){
                                                    if(!isNull(demiFrame1.demiFrame.name)){
                                                        var frameName = demiFrame1.demiFrame.name;
                                                        $('.info-text p[name="frame"] span').text(frameName).attr("data",frameName);
                                                    }else{
                                                        $('.info-text p[name="frame"] span').text("无");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $('.info-text p[name="frame"] span').text("无卡纸");
                    }
                    if(!isNull(products.productFronts) && products.productFronts != 0){
                        for(var m in products.productFronts){
                            if(!isNull(products.productFronts[m].front) && products.productFronts[m].front != 0){
                                var front = products.productFronts[m].front;
                                if(!isNull(front.frontPattern) && front.frontPattern != 0){
                                    for(var n in front.frontPattern){
                                        if(!isNull(front.frontPattern[n].frontPatternDemi) && front.frontPattern[n].frontPatternDemi != 0){
                                            for(var o in front.frontPattern[n].frontPatternDemi){
                                                var demiFront = front.frontPattern[n].frontPatternDemi[o];
                                                if(!isNull(demiFront.demiFront) && demiFront.demiFront != 0){
                                                    if(!isNull(demiFront.demiFront.name)){
                                                        var frontName = demiFront.demiFront.name;
                                                        $('.info-text p[name="front"] span').text(frontName).attr("data",frontName);
                                                    }else{
                                                        $('.info-text p[name="front"] span').text("无");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!isNull(products.productBacks) && products.productBacks != 0){
                        for(var m in products.productBacks){
                            if(!isNull(products.productBacks[m].back) && products.productBacks[m].back != 0){
                                var back = products.productBacks[m].back;
                                if(!isNull(back.backPattern) && back.backPattern != 0){
                                    for(var n in back.backPattern){
                                        if(!isNull(back.backPattern[n].backPatternDemi) && back.backPattern[n].backPatternDemi != 0){
                                            for(var o in back.backPattern[n].backPatternDemi){
                                                var demiBack = back.backPattern[n].backPatternDemi[o];
                                                if(!isNull(demiBack.demiBack) && demiBack.demiBack != 0){
                                                    if(!isNull(demiBack.demiBack.name)){
                                                        var backName = demiBack.demiBack.name;
                                                        $('.info-text p[name="back"] span').text(backName).attr("data",backName);
                                                    }else{
                                                        $('.info-text p[name="back"] span').text("无");
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
            }
        }
    }
}
