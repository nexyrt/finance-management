interface Sparkline30DaysProps {
    data: number[];
    width?: number;
    height?: number;
    className?: string;
}

export default function Sparkline30Days({ data, width = 200, height = 48, className = '' }: Sparkline30DaysProps) {
    if (!data || data.length < 2) {
        return (
            <svg width={width} height={height} className={className}>
                <line x1={0} y1={height / 2} x2={width} y2={height / 2} stroke="currentColor" strokeOpacity={0.2} strokeWidth={1} />
            </svg>
        );
    }

    const padV = 4;
    const min = Math.min(...data);
    const max = Math.max(...data);
    const range = max - min || 1;

    const points = data.map((v, i) => {
        const x = (i / (data.length - 1)) * width;
        const y = padV + (1 - (v - min) / range) * (height - padV * 2);
        return `${x},${y}`;
    });

    const polylineStr = points.join(' ');

    // Area fill path
    const firstX = 0;
    const lastX = width;
    const baseY = height - padV;
    const areaPath = `M${firstX},${baseY} L${points.join(' L')} L${lastX},${baseY} Z`;

    const gradId = `spark-grad-${Math.random().toString(36).slice(2, 7)}`;
    const lastPoint = points[points.length - 1].split(',');
    const dotX = parseFloat(lastPoint[0]);
    const dotY = parseFloat(lastPoint[1]);

    const isPositive = (data[data.length - 1] ?? 0) >= 0;
    const strokeColor = isPositive ? '#34d399' : '#f87171';
    const fillColor = isPositive ? '#10b981' : '#ef4444';

    return (
        <svg width={width} height={height} className={className} overflow="visible">
            <defs>
                <linearGradient id={gradId} x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor={fillColor} stopOpacity={0.25} />
                    <stop offset="100%" stopColor={fillColor} stopOpacity={0} />
                </linearGradient>
            </defs>
            {/* Area */}
            <path d={areaPath} fill={`url(#${gradId})`} />
            {/* Line */}
            <polyline
                points={polylineStr}
                fill="none"
                stroke={strokeColor}
                strokeWidth={1.5}
                strokeLinecap="round"
                strokeLinejoin="round"
            />
            {/* Latest dot with glow */}
            <circle cx={dotX} cy={dotY} r={4} fill={strokeColor} opacity={0.3} />
            <circle cx={dotX} cy={dotY} r={2.5} fill={strokeColor} />
        </svg>
    );
}
