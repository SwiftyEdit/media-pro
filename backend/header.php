<?php


?>

<style>
    @keyframes pulse {
        50% {
            background: white;
        }
    }

    .loading-pulse {
        position: relative;
        float: left;
        margin-right: 25px;
        width: 6px;
        height: 18px;
        background: rgba(255, 255, 255, 0.2);
        -webkit-animation: pulse 750ms infinite;
        animation: pulse 750ms infinite;
        -webkit-animation-delay: 250ms;
        animation-delay: 250ms;
    }
    .loading-pulse:before, .loading-pulse:after {
        content: "";
        position: absolute;
        display: block;
        height: 12px;
        width: 6px;
        background: rgba(255, 255, 255, 0.2);
        top: 50%;
        transform: translateY(-50%);
        -webkit-animation: pulse 750ms infinite;
        animation: pulse 750ms infinite;
    }
    .loading-pulse:before {
        left: -12px;
    }
    .loading-pulse:after {
        left: 12px;
        -webkit-animation-delay: 500ms;
        animation-delay: 500ms;
    }
</style>