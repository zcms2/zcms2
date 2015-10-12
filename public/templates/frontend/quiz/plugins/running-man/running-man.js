var w = c.width = window.innerWidth,
    h = c.height = window.innerHeight,
    ctx = c.getContext( '2d' ),

    dude = {

        body: seg( 30, 10 ),
        leg1: seg( 30, 9 ),
        leg2: seg( 30, 9 ),
        lowerLeg1: seg( 20, 6 ),
        lowerLeg2: seg( 20, 6 ),
        arm1: seg( 20, 8 ),
        arm2: seg( 20, 8 ),
        lowerArm1: seg( 15, 5 ),
        lowerArm2: seg( 15, 5 ),

        x: w / 3,
        y: h / 2,
        vx: 0,
        vy: 0
    },

    tick = 0,
    its = 5;

function seg( length, width ){

    return {
        l: length,
        w: width,
        r: 0 }
}
function getPin( seg ){

    return {
        x: Math.cos( seg.r ) * seg.l,
        y: Math.sin( seg.r ) * seg.l
    }
}
function useSeg( seg, x, y, nw ){

    var wd = nw - seg.w,
        pin = getPin( seg ),
        dxi = pin.x / its,
        dyi = pin.y / its,
        x = x,
        y = y;

    pin.x += x;
    pin.y += y;

    for( var i = 0; i < its; ++i ){

        var prop = i / its;

        ctx.lineWidth = seg.w + wd * prop;
        ctx.beginPath();
        ctx.moveTo( x, y );
        x += dxi;
        y += dyi;
        ctx.lineTo( x + dxi / 10, y + dyi / 10 );
        ctx.stroke();
    }

    return pin;
}

dude.body.r = Math.PI / 1.6;

function anim(){

    window.requestAnimationFrame( anim );

    ctx.fillStyle = '#e80';
    ctx.fillRect( 0, 0, w, h / 2 + 78 );

    ctx.fillStyle = '#a40';
    ctx.fillRect( 0, h / 2 + 78, w, h - ( h / 2 + 78 ) );

    ctx.fillStyle = 'black';

    tick += .08;

    dude.x += dude.vx;
    dude.y += dude.vy;

    var pin = pin2 = useSeg( dude.body, dude.x, dude.y, dude.leg1.w );

    dude.leg1.r = Math.sin( tick ) * Math.PI / 4 + Math.PI / 2;
    dude.leg2.r = Math.sin( tick + Math.PI ) * Math.PI / 4 + Math.PI / 2;

    dude.lowerLeg1.r = dude.leg1.r + Math.sin( tick - Math.PI / 2 ) * Math.PI / 4 + Math.PI / 4;
    dude.lowerLeg2.r = dude.leg2.r + Math.sin( tick + Math.PI / 2 ) * Math.PI / 4 + Math.PI / 4;

    pin = useSeg( dude.leg1, pin.x, pin.y, dude.lowerLeg1.w );
    useSeg( dude.lowerLeg1, pin.x, pin.y, 0 );

    pin = useSeg( dude.leg2, pin2.x, pin2.y, dude.lowerLeg2.w );
    useSeg( dude.lowerLeg2, pin.x, pin.y, 0 );

    dude.arm1.r = Math.sin( tick + 1 ) * Math.PI / 2 + Math.PI / 2;
    dude.arm2.r = Math.sin( tick + Math.PI + 1 ) * Math.PI / 2 + Math.PI / 2;

    dude.lowerArm1.r = dude.arm1.r + Math.sin( tick - Math.PI / 2 + 1 ) * Math.PI / 8 - Math.PI / 4;
    dude.lowerArm2.r = dude.arm2.r + Math.sin( tick + Math.PI / 2 + 1 ) * Math.PI / 8 - Math.PI / 4;

    pin = useSeg( dude.arm1, dude.x, dude.y, dude.lowerArm1.w );
    useSeg( dude.lowerArm1, pin.x, pin.y, 0 );

    pin = useSeg( dude.arm2, dude.x, dude.y, dude.lowerArm2.w );
    useSeg( dude.lowerArm2, pin.x, pin.y, 0 );

    ctx.beginPath();
    ctx.arc( dude.x - Math.cos( dude.body.r ) * 12, dude.y - Math.sin( dude.body.r ) * 12, 6, 0, Math.PI * 2 );
    ctx.fill();
}

anim();